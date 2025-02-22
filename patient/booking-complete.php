<?php
session_start();

if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Import bazy danych
include("../connection.php");

// Pobranie informacji o użytkowniku
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();

$userid = $userfetch["pid"];
$username = $userfetch["pname"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["booknow"]) && isset($_POST["scheduleid"]) && isset($_POST["date"])) {
        // Generowanie numeru wizyty (jeśli nie jest przekazany)
        $apponum = $_POST["apponum"] ?? rand(1000, 9999); // Domyślna wartość, jeśli nie podano
        
        $scheduleid = $_POST["scheduleid"];
        $date = $_POST["date"];

        // Sprawdzenie, czy wartości są poprawne
        if (!is_numeric($scheduleid) || !is_numeric($apponum)) {
            die("Błąd: Niepoprawne dane wejściowe.");
        }

        // Bezpieczne zapytanie SQL
        $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) VALUES (?, ?, ?, ?)";
        $stmt = $database->prepare($sql2);
        $stmt->bind_param("iiis", $userid, $apponum, $scheduleid, $date);
        
        if ($stmt->execute()) {
            header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
            exit();
        } else {
            die("Błąd przy dodawaniu rezerwacji: " . $stmt->error);
        }
    } else {
        die("Błąd: Brak wymaganych danych.");
    }
} else {
    die("Błąd: Niepoprawne żądanie.");
}
?>
