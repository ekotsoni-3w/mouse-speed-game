<!DOCTYPE html> <!-- Δηλώνει ότι χρησιμοποιούμε HTML5 -->
<html lang="el"> <!-- Η γλώσσα της σελίδας είναι ελληνικά -->

<head>
    <meta charset="UTF-8"> <!-- Υποστήριξη ελληνικών χαρακτήρων -->
    <title>Emoji Mouse Game</title> <!-- Τίτλος καρτέλας -->
    <link rel="stylesheet" href="style.css?v=2"> <!-- Σύνδεση CSS -->
</head>

<body>

<!-- ================= INSTRUCTIONS MODAL ================= -->

<div id="instructionsModal" class="modal">
    <div class="modal-content">
        <h2>📜 Οδηγίες Παιχνιδιού</h2>
        <p>
            🖱 Κούνα το ποντίκι μέσα στο άσπρο πλαίσιο.<br>
            ⭐ Κάθε κίνηση σου δίνει πόντους.<br>
            ⏳ Έχεις 10 δευτερόλεπτα.
        </p>
        <button onclick="startFromInstructions()">Ξεκίνα!</button>
    </div>
</div>

<!-- ================= GAME OVER MODAL ================= -->

<div id="gameOverModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h2>🏁 Game Over</h2>
        <p>Το σκορ σου είναι:</p>
        <h3 id="finalScore">0</h3>
        <button onclick="restartGame()">Παίξε Ξανά</button>
    </div>
</div>

<!-- ================= MAIN GAME CONTENT ================= -->

<div id="mainContent">

<h1>🎮 Emoji Mouse Game</h1>

<div class="info">
    ⏳ Χρόνος: <span id="time">10</span> |
    ⭐ Σκορ: <span id="score">0</span>
</div>

<button onclick="startGame()">Play</button>
<button onclick="stopGame()">Stop</button>

<div id="gameArea"></div>

</div>

<!-- ================= JAVASCRIPT ================= -->

<script>

// Μεταβλητές παιχνιδιού
let score = 0;          // Πόντοι
let time = 10;          // Χρόνος
let timer;              // Θα κρατάει το setInterval
let gameRunning = false;// Αν το παιχνίδι είναι ενεργό

// Όταν φορτώνει η σελίδα
window.onload = function() {
    document.getElementById("mainContent").classList.add("blur"); 
    // Κάνει blur το background μέχρι να πατήσει "Ξεκίνα"
}

// Ξεκίνημα από τις οδηγίες
function startFromInstructions() {
    document.getElementById("instructionsModal").style.display = "none"; 
    // Κρύβει το modal οδηγιών
    
    document.getElementById("mainContent").classList.remove("blur"); 
    // Αφαιρεί το blur

    startGame(); // Ξεκινά το παιχνίδι
}

// Ξεκινά το παιχνίδι
function startGame() {

    if (gameRunning) return; // Αν ήδη παίζει, μην ξαναξεκινήσεις

    score = 0; // Μηδενισμός σκορ
    time = 10; // Επαναφορά χρόνου
    gameRunning = true; // Το παιχνίδι είναι ενεργό

    document.getElementById("score").innerText = score; // Εμφάνιση σκορ
    document.getElementById("time").innerText = time;   // Εμφάνιση χρόνου

    // Μετρητής αντίστροφης μέτρησης
    timer = setInterval(function() {
        time--; // Μείωση χρόνου

        document.getElementById("time").innerText = time;

        if (time <= 0) { // Αν τελειώσει ο χρόνος
            stopGame();  // Σταμάτα το παιχνίδι
        }

    }, 1000); // Κάθε 1 δευτερόλεπτο
}

// Σταματά το παιχνίδι
function stopGame() {

    gameRunning = false; // Το παιχνίδι σταματά
    clearInterval(timer); // Σταματά το setInterval

    // Αποθήκευση σκορ στη βάση
    fetch("save_score.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "score=" + score
    });

    // Εμφάνιση Game Over modal
    document.getElementById("finalScore").innerText = score;
    document.getElementById("gameOverModal").style.display = "flex";
    document.getElementById("mainContent").classList.add("blur");
}

// Restart παιχνιδιού
function restartGame() {

    document.getElementById("gameOverModal").style.display = "none"; 
    document.getElementById("mainContent").classList.remove("blur");

    startGame(); // Ξεκινά νέο παιχνίδι
}

// Όταν κινείται το ποντίκι μέσα στο gameArea
document.getElementById("gameArea").addEventListener("mousemove", function(e) {

    if (!gameRunning) return; // Αν δεν παίζει, μη μετράς

    score++; // Αύξηση σκορ

    document.getElementById("score").innerText = score;

    createEmoji(e.clientX, e.clientY); // Δημιουργία emoji
});

// Δημιουργία emoji animation
function createEmoji(x, y) {

    let emoji = document.createElement("div"); // Δημιουργεί div
    emoji.classList.add("emoji"); // Προσθέτει class
    emoji.innerText = "✨"; // Το emoji που εμφανίζεται

    emoji.style.left = x + "px"; // Θέση X
    emoji.style.top = y + "px";  // Θέση Y

    document.body.appendChild(emoji); // Προσθήκη στο body

    setTimeout(() => {
        emoji.remove(); // Διαγραφή μετά από 1 δευτερόλεπτο
    }, 1000);
}

</script>

</body>
</html>