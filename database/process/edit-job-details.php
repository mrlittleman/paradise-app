<?php
include '../connection.php'; 
$response = [];
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = intval($_POST['job_id']); 
    $job_title = trim($_POST['job_title']); 
    $job_short_desc = trim($_POST['job_short_desc']); 
    $job_desc = trim($_POST['job_desc']); 
    $date = date('Y-m-d'); 

    if (empty($jobId) || empty($job_title) || empty($job_short_desc) || empty($job_desc)) {
        $response['success'] = false;
        $response['error'] = "Missing required fields.";
        echo json_encode($response);
        exit;
    }


    $query = "UPDATE job_application SET job_title = ?, job_short_desc = ?, job_desc = ?, date = ? WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssi", $job_title, $job_short_desc, $job_desc, $date, $jobId);
        if ($stmt->execute()) {
            $response['success'] = true; 
        } else {
            $response['success'] = false;
            $response['error'] = "Failed to update job.";
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
