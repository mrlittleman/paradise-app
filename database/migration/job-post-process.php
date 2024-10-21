<?php
include '../connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_title = $_POST['job_title'];
    $job_short_desc = $_POST['job_short_desc'];
    $job_desc = $_POST['job_desc'];
    $date = date('Y-m-d'); 
    $stmt = $conn->prepare("INSERT INTO job_application (job_title, job_short_desc, job_desc, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $job_title, $job_short_desc, $job_desc, $date);
    $stmt->execute(); 
    $stmt->close();
}
$conn->close();
?>
