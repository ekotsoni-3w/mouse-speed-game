<?php

// Δημιουργία σύνδεσης με MySQL
$connection = new mysqli("localhost", "root", "", "game_db");

// Έλεγχος αν απέτυχε η σύνδεση
if ($connection->connect_error) {
    die("Σφάλμα σύνδεσης");
}

// Παίρνουμε το σκορ από το POST
$score = $_POST['score'];

// SQL εντολή εισαγωγής
$sql = "INSERT INTO scores (score) VALUES ('$score')";

// Εκτέλεση εντολής
$connection->query($sql);

// Κλείσιμο σύνδεσης
$connection->close();

?>