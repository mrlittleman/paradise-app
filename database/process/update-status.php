<?php
include './database/connection.php'; 

$data = json_decode(file_get_contents('php://input'), true);
$applicantId = $data['id'];
$status = $data['status'];


$stmt = $conn->prepare("UPDATE applicants SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $applicantId);

$response = ['success' => false];

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['error'] = $stmt->error;
}

$stmt->close();
header('Content-Type: application/json');
echo json_encode($response);

$conn->close(); 
?>
