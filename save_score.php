<?php

// Η απάντηση του endpoint θα είναι σε μορφή JSON.
header("Content-Type: application/json; charset=UTF-8");

// Μέγιστη αποδεκτή τιμή για προστασία από παράλογα δεδομένα.
const MAX_SCORE = 100000;

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

// Μετατρέπουμε τα σφάλματα της MySQL σε exceptions.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try
{
    // Δημιουργία σύνδεσης με MySQL
    $connection = new mysqli(
        "127.0.0.1",
        "root",
        "",
        "game_db"
    );

    // Υποστήριξη όλων των χαρακτήρων Unicode.
    $connection->set_charset("utf8mb4");

    // Το ερωτηματικό είναι placeholder για το score.
    $statement = $connection->prepare(
        "INSERT INTO scores (score) VALUES (?)"
    );

    // Το "i" δηλώνει ότι το score είναι integer.
    $statement->bind_param("i", $score);

    // Εκτέλεση του prepared statement.
    $statement->execute();

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