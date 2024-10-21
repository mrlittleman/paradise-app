<?php
include '../connection.php'; // Adjust path as necessary
$response = [];
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $jobId = intval($data['job_id']);

    $query = "DELETE FROM job_application WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $jobId);
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = "Failed to delete job.";
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['error'] = "Database error.";
    }

    $conn->close();
} else {
    $response['success'] = false;
    $response['error'] = "Invalid request method.";
}

ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
