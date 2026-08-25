<?php

// Ασφαλής έναρξη session για τον έλεγχο συχνότητας υποβολών.
session_start([
    "use_strict_mode" => true,
    "cookie_httponly" => true,
    "cookie_samesite" => "Lax",
    "cookie_secure" => isset($_SERVER["HTTPS"])
        && $_SERVER["HTTPS"] !== "off"
]);

// Η απάντηση του endpoint θα είναι σε μορφή JSON.
header("Content-Type: application/json; charset=UTF-8");

// Μέγιστη αποδεκτή τιμή για προστασία από παράλογα δεδομένα.
const MAX_SCORE = 100000;

// Ελάχιστος χρόνος μεταξύ δύο επιτυχημένων υποβολών.
const SCORE_SUBMISSION_COOLDOWN_SECONDS = 5;

// Ανώτατος αριθμός scores που διατηρεί το demo.
const MAX_STORED_SCORES = 10000;

// Το endpoint δέχεται μόνο POST requests.
if ($_SERVER["REQUEST_METHOD"] !== "POST")
{
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}

// Περιορίζουμε τις συνεχόμενες υποβολές από το ίδιο session.
$lastSubmissionTime = $_SESSION["last_score_submission_at"] ?? null;

if (
    $lastSubmissionTime !== null
    && time() - $lastSubmissionTime < SCORE_SUBMISSION_COOLDOWN_SECONDS
)
{
    http_response_code(429);
    header("Retry-After: " . SCORE_SUBMISSION_COOLDOWN_SECONDS);

    echo json_encode([
        "success" => false,
        "message" => "Please wait before submitting another score."
    ]);

    exit;
}

// Ελέγχουμε ότι το score υπάρχει και είναι ακέραιος αριθμός μεταξύ 0 και MAX_SCORE.
$score = filter_input(
    INPUT_POST,
    "score",
    FILTER_VALIDATE_INT,
    [
        "options" => [
            "min_range" => 0,
            "max_range" => MAX_SCORE
        ]
    ]
);

if ($score === false || $score === null)
{
    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "The score must be a valid integer."
    ]);

    exit;
}

// Διαδρομή προς το τοπικό αρχείο ρυθμίσεων.
$configPath = __DIR__ . "/config.php";

// Το configuration δεν αποθηκεύεται στο Git και πρέπει να δημιουργηθεί τοπικά.
if (!is_file($configPath))
{
    error_log("Database configuration file not found.");

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "The score could not be saved."
    ]);

    exit;
}

// Φόρτωση των στοιχείων σύνδεσης.
$config = require $configPath;

// Μετατρέπουμε τα σφάλματα της MySQL σε exceptions.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try
{
    // Δημιουργία σύνδεσης με MySQL
    $connection = new mysqli(
        $config["host"],
        $config["username"],
        $config["password"],
        $config["database"],
        $config["port"]
    );

    // Υποστήριξη όλων των χαρακτήρων Unicode.
    $connection->set_charset("utf8mb4");

    // Προστασία της demo βάσης από απεριόριστη αύξηση.
    $countResult = $connection->query(
        "SELECT COUNT(*) AS total FROM scores"
    );

    $totalScores = (int) $countResult->fetch_assoc()["total"];
    $countResult->free();

    if ($totalScores >= MAX_STORED_SCORES)
    {
        http_response_code(503);

        echo json_encode([
            "success" => false,
            "message" => "Score storage is temporarily unavailable."
        ]);

        $connection->close();
        exit;
    }

    // Το ερωτηματικό είναι placeholder για το score.
    $statement = $connection->prepare(
        "INSERT INTO scores (score) VALUES (?)"
    );

    // Το "i" δηλώνει ότι το score είναι integer.
    $statement->bind_param("i", $score);

    // Εκτέλεση του prepared statement.
    $statement->execute();

    // Καταγράφουμε τον χρόνο της επιτυχημένης υποβολής.
    $_SESSION["last_score_submission_at"] = time();

    // Το 201 σημαίνει ότι δημιουργήθηκε επιτυχώς νέα εγγραφή.
    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Score saved successfully."
    ]);

    $statement->close();
    $connection->close();
}
catch (mysqli_sql_exception $exception)
{
    // Καταγράφουμε το πραγματικό σφάλμα μόνο στα logs του server.
    error_log("Database error: " . $exception->getMessage());

    // Δεν αποκαλύπτουμε λεπτομέρειες της βάσης στον χρήστη.
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "The score could not be saved."
    ]);
}