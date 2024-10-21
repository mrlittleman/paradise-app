<?php
include './database/connection.php';
$message = "";

if (isset($_GET['id'])) {
    $jobId = intval($_GET['id']);
    $query = "SELECT * FROM job_application WHERE id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $jobId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $job = $result->fetch_assoc();
        } else {
            $message = "Job not found.";
        }

        $stmt->close();
    }
} else {
    $message = "No job ID specified.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/job-details.css">
    <title>Job Details</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .job-details-container {
            margin: 3rem 10rem;
        }

        .job-details-container h1 {
            color: #28a745;
        }

        .button-contents {
            margin-top: 2rem;
        }

        .button-contents button {
            margin-right: 2rem;
            padding: 0.5rem 1rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button-contents button:hover {
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
            justify-content: center; /* Center the modal */
            align-items: center; /* Center the modal */
        }

        .modal-content {
            background-color: #fff; /* White background */
            border-radius: 10px; /* Rounded corners */
            padding: 2rem; /* Padding */
            max-width: 600px; /* Max width */
            width: 90%; /* Responsive width */
            text-align: center; /* Center text */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow */
            animation: slideDown 0.3s ease-in-out; /* Animation */
        }

        .modal h2 {
            color: #333; /* Dark text color */
            margin-bottom: 1rem; /* Margin bottom */
        }

        .modal p {
            margin-bottom: 1.5rem; /* Margin bottom */
        }

        .modal button {
            padding: 0.75rem 1.5rem; /* Button padding */
            margin: 0.5rem; /* Button margin */
            border-radius: 4px; /* Rounded corners */
            border: none; /* No border */
            font-size: 1rem; /* Font size */
            cursor: pointer; /* Pointer cursor */
            background-color: #28a745; /* Button color */
            color: white; /* Text color */
        }

        .modal button.cancel-btn {
            background-color: #ccc; /* Cancel button color */
            color: #333; /* Cancel button text color */
        }

        .modal button:hover {
            opacity: 0.9; /* Hover effect */
        }

        .close {
            position: absolute; /* Positioning */
            top: 1rem; /* Top position */
            right: 1rem; /* Right position */
            font-size: 1.5rem; /* Font size */
            cursor: pointer; /* Pointer cursor */
            color: #999; /* Close button color */
        }

        /* Animation for modal fade and slide */
        @keyframes slideDown {
            from { transform: translateY(-10px); }
            to { transform: translateY(0); }
        }
    </style>
</head>
<body>
    <main>
        <div class="job-details-container">
            <?php if (isset($job)): ?>
                <h1 id="job-title"><?php echo htmlspecialchars($job["job_title"]); ?></h1>
                <p id="job-date">Date: <?php echo htmlspecialchars($job["date"]); ?></p>
                <p id="job-short-desc"><?php echo htmlspecialchars($job["job_short_desc"]); ?></p>
                <p id="job-desc"><?php echo nl2br(htmlspecialchars($job["job_desc"])); ?></p>
            <?php else: ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
            
            <div class="button-contents">
                <button onclick="openEditModal()">Edit</button>
                <button onclick="openDeleteModal()">Delete</button>
                <button onclick="openApplyModal()">Apply</button>
            </div>

            <!-- Separate Edit Form -->
            <div id="editFormContainer" style="display: none;">
                <h2>Edit Job Details</h2>
                <form id="editForm">
                    <input type="hidden" name="job_id" value="<?php echo $jobId; ?>">
                    <label for="job_title">Job Title:</label>
                    <input type="text" id="job_title_input" name="job_title" value="<?php echo htmlspecialchars($job["job_title"]); ?>" required>
                    
                    <label for="job_short_desc">Short Description:</label>
                    <textarea id="job_short_desc_input" name="job_short_desc" required><?php echo htmlspecialchars($job["job_short_desc"]); ?></textarea>
                    
                    <label for="job_desc">Description:</label>
                    <textarea id="job_desc_input" name="job_desc" required><?php echo htmlspecialchars($job["job_desc"]); ?></textarea>
                    
                    <button type="submit">Save Changes</button>
                    <button type="button" onclick="closeEditForm()">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <div class="deletion">
                    <span class="close" onclick="closeDeleteModal()">&times;</span>
                    <h2>Are you sure you want to delete this job?</h2>
                    <button id="confirmDeleteButton">Yes, Delete</button>
                    <button onclick="closeDeleteModal()">Cancel</button>
                </div>
            </div>
        </div>

        <!-- Apply Modal -->
        <div id="applyModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeApplyModal()">&times;</span>
                <h2>Apply for Job</h2>
                <form id="applyForm">
                    <input type="hidden" name="job_id" value="<?php echo $jobId; ?>">
                    <label for="applicantName">Name:</label>
                    <input type="text" id="applicantName" name="name" required>
                    
                    <label for="applicantEmail">Email:</label>
                    <input type="email" id="applicantEmail" name="email" required>
                    
                    <label for="applicantMessage">Cover Letter:</label>
                    <textarea id="applicantMessage" name="message" required></textarea>
                    
                    <button type="submit">Submit Application</button>
                </form>
            </div>
        </div>

    </main>

    <script>
        function openEditModal() {
            document.getElementById('editFormContainer').style.display = 'block';
        }

        function closeEditForm() {
            document.getElementById('editFormContainer').style.display = 'none';
        }

        document.getElementById('editForm').addEventListener('submit', function(event) {
            event.preventDefault(); 

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            fetch('./database/process/edit-job-details.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false; 
                if (data.success) {
                    document.getElementById('job-title').innerText = formData.get('job_title');
                    document.getElementById('job-short-desc').innerText = formData.get('job_short_desc');
                    document.getElementById('job-desc').innerText = formData.get('job_desc');
                    closeEditForm(); 
                } else {
                    alert('Error: ' + data.error); 
                }
            })
            .catch(error => {
                submitButton.disabled = false; 
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            });
        });

        function openDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            const jobId = <?php echo $jobId; ?>;

            fetch('./database/process/delete-job-details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ job_id: jobId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = './index.php?page=job-postings'; 
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            });
        });

        function openApplyModal() {
            document.getElementById('applyModal').style.display = 'block';
        }

        function closeApplyModal() {
            document.getElementById('applyModal').style.display = 'none';
        }

        document.getElementById('applyForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            fetch('./database/process/apply-job.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                if (data.success) {
                    alert('Application submitted successfully!');
                    closeApplyModal();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                submitButton.disabled = false;
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            });
        });
    </script>
</body>
</html>
