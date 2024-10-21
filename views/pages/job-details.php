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

<style>
    /* General modal styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease-in-out;
    }

    .modal-content {
        background-color: #fff;
        border-radius: 8px;
        padding: 2rem;
        max-width: 600px;
        width: 100%;
        margin: auto;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        animation: slideDown 0.3s ease-in-out;
    }

    .modal h2 {
        color: #333;
        margin-bottom: 1rem;
    }

    /* Form-specific styling */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 0.5rem;
        text-align: left;
    }

    .form-group input, .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1rem;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Centered modal button layout */
    .modal-buttons {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center; /* Center the buttons */
        gap: 1rem;
    }

    .modal-buttons button {
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        border: none;
        font-size: 1rem;
        cursor: pointer;
    }

    .modal button.cancel-btn {
        background-color: #ccc;
        color: #333;
    }

    .modal button.confirm-btn {
        background-color: #2ecc71;
        color: #fff;
    }

    .modal button:hover {
        opacity: 0.9;
    }
    .job-details-container h1{
        color: #2e7d32;
    }
    .job-details-container {
        margin-left: 5rem;
        margin-right: 5rem;
        margin-bottom: 6rem;
    }
    .job-details-container button {
        margin-right: 1rem;
    }

    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }

    @keyframes slideDown {
        from {transform: translateY(-10px);}
        to {transform: translateY(0);}
    }
</style>

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
            <button onclick="window.location.href='./index.php?page=apply-job'">Apply</button>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Job Details</h2>
            <form id="editForm">
                <input type="hidden" name="job_id" value="<?php echo $jobId; ?>">

                <div class="form-group">
                    <label for="job_title">Job Title:</label>
                    <input type="text" id="job_title_input" name="job_title" value="<?php echo htmlspecialchars($job["job_title"]); ?>" required>
                </div>

                <div class="form-group">
                    <label for="job_short_desc">Short Description:</label>
                    <textarea id="job_short_desc_input" name="job_short_desc" required><?php echo htmlspecialchars($job["job_short_desc"]); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="job_desc">Description:</label>
                    <textarea id="job_desc_input" name="job_desc" required><?php echo htmlspecialchars($job["job_desc"]); ?></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="submit" class="confirm-btn">Save Changes</button>
                    <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Are you sure you want to delete this job?</h2>
            <p>This action cannot be undone. The job and all related data will be permanently deleted.</p>
            <div class="modal-buttons">
                <button class="confirm-btn" id="confirmDeleteButton">Yes, Delete</button>
                <button class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="./styles/job-details.css">
</main>

<script>
    // Modal handling
    function openEditModal() {
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Form submission for editing
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
                closeEditModal(); 
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

    // Deletion confirmation
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
</script>
