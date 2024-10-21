<?php
include './database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $applicant_name = $_POST['applicant_name'];
    $applicant_email = $_POST['applicant_email'];
    $job_title = $_POST['job_title']; // Get job title from POST data

    $resume_path = 'uploads/' . basename($_FILES['resume']['name']);
    if (move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
        $status = 'applied'; 
        $applied_at = date("Y-m-d H:i:s"); 

        // Include job_title in the INSERT query
        $stmt = $conn->prepare("INSERT INTO applicants (applicant_name, applicant_email, job_title, resume_path, status, applied_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $applicant_name, $applicant_email, $job_title, $resume_path, $status, $applied_at);

        if ($stmt->execute()) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { showSuccessPopup(); });</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading resume.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            transition: background-color 0.3s, color 0.3s;
        }

        body.dark-mode {
            background-color: #121212;
            color: #ffffff;
        }

        .job-application-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, color 0.3s;
        }

        body.dark-mode .job-application-container {
            background: #1e1e1e;
        }

        .job-application-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        body.dark-mode .job-application-container h1 {
            color: #ffffff;
        }

        .job-application-container .form-group {
            margin-bottom: 20px;
        }

        .job-application-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        body.dark-mode .job-application-container label {
            color: #dddddd;
        }

        .job-application-container input[type="text"],
        .job-application-container input[type="email"],
        .job-application-container input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        body.dark-mode .job-application-container input[type="text"],
        body.dark-mode .job-application-container input[type="email"] {
            border-color: #555;
            background: #333;
            color: #fff;
        }

        .job-application-container input[type="file"] {
            background: #fff;
        }

        body.dark-mode .job-application-container input[type="file"] {
            background: #333;
            color: #fff;
        }

        .job-application-container button {
            width: 100%;
            padding: 12px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .job-application-container button:hover {
            background-color: #27632c;
        }

        body.dark-mode .job-application-container button {
            background-color: #4CAF50;
        }

        body.dark-mode .job-application-container button:hover {
            background-color: #45a049;
        }

        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #2e7d32;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            padding: 20px;
            text-align: center;
            transition: background-color 0.3s, color 0.3s;
        }

        body.dark-mode .popup {
            background-color: #1e1e1e;
            color: #ffffff;
        }

        .popup h2 {
            color: #2e7d32;
        }

        body.dark-mode .popup h2 {
            color: #ffffff;
        }

        .toggle-button {
            margin: 20px;
            padding: 10px 15px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="job-application-container">
        <h1>Job Application</h1>
        <form id="application-form" method="POST" enctype="multipart/form-data" action="">
            <div class="form-group">
                <label for="applicant_name">Full Name:</label>
                <input type="text" id="applicant_name" name="applicant_name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="applicant_email">Email Address:</label>
                <input type="email" id="applicant_email" name="applicant_email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" placeholder="Position you are applying for" required>
            </div>
            <div class="form-group">
                <label for="resume">Resume (PDF, DOC, DOCX):</label>
                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                <small>Max file size: 2MB</small>
            </div>
            <button type="submit">Submit Application</button>
        </form>
    </div>

    <div class="popup" id="successPopup">
        <h2>Application Submitted Successfully!</h2>
        <p>Thank you for applying. We will review your application and get back to you soon.</p>
        <button onclick="closePopup()">Close</button>
    </div>

    <script>
        function showSuccessPopup() {
            document.getElementById('successPopup').style.display = 'block';
        }
        function closePopup() {
            document.getElementById('successPopup').style.display = 'none';
            window.location.href = './index.php?'; 
        }
        document.getElementById('application-form').addEventListener('submit', function(event) {
            const resumeInput = document.getElementById('resume');
            const filePath = resumeInput.value;

            const allowedExtensions = /(\.pdf|\.doc|\.docx)$/i;
            if (!allowedExtensions.exec(filePath)) {
                alert('Please upload a file with .pdf, .doc, or .docx extension.');
                resumeInput.value = '';
                event.preventDefault();
                return;
            }

            const file = resumeInput.files[0];
            if (file && file.size > 2 * 1024 * 1024) {
                alert('File size must not exceed 2MB.');
                resumeInput.value = '';
                event.preventDefault();
            }
        });

    </script>
</body>
</html>
