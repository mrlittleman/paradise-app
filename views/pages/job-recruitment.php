<?php
include './database/connection.php';

$query = "SELECT applied_at, id, applicant_email, applicant_name, job_title, resume_path, status FROM applicants";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$applicants = $result->fetch_all(MYSQLI_ASSOC);
?>

<style>
    :root {
        --primary-color: #2e7d32;
        --primary-hover: #27632c;
        --red-color: #f44336;
        --red-hover: #e53935;
        --background-color: #f4f4f4;
        --modal-background: #fff;
        --text-color: black;
        --transition: background-color 0.3s, color 0.3s;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        margin: 0;
        padding: 20px;
        transition: var(--transition);
    }

    body.dark-mode {
        background-color: #121212;
        color: white;
    }

    .applicants-container {
        background: var(--modal-background);
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
    }

    body.dark-mode .applicants-container {
        background: #1e1e1e;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        color: var(--primary-color);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    body.dark-mode th {
        background-color: #333;
        color: white;
    }

    body.dark-mode td {
        color: white;
    }

    .proceed-button, .reject-button {
        padding: 10px 15px;
        cursor: pointer;
        border: none;
        color: white;
        border-radius: 5px;
        transition: background-color 0.3s, color 0.3s;
    }

    body.dark-mode .proceed-button, .reject-button {
        border-radius: 5px;
        transition: background-color 0.3s, color 0.3s;
    }
    body.dark-mode .reject-button {
        border-radius: 5px;
        transition: background-color 0.3s, color 0.3s;
    }
    .proceed-button {
        background-color: var(--primary-color);
    }

    .proceed-button:hover {
        background-color: var(--primary-hover);
        color: white;
    }

    .reject-button {
        background-color: var(--red-color);
    }

    .reject-button:hover {
        background-color: var(--red-hover);
        color: white;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: var(--modal-background);
        margin: 5% auto;
        padding: 20px;
        border: 1px solid var(--primary-color);
        width: 80%;
        max-width: 400px;
        border-radius: 5px;
        transition: var(--transition);
    }

    body.dark-mode .modal-content {
        background-color: #1e1e1e;
        border: 1px solid var(--primary-color);
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: var(--primary-color);
        text-decoration: none;
        cursor: pointer;
    }

    a {
        color: var(--text-color);
        text-decoration: none;
    }
</style>

<div class="applicants-container">
    <h1>Applicants List</h1>
    <table>
        <thead>
            <tr>
                <th>Applied At</th>
                <th>ID</th>
                <th>Email</th>
                <th>Name</th>
                <th>Job Title</th>
                <th>Resume</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($applicants)): ?>
                <?php foreach ($applicants as $applicant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($applicant['applied_at']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['id']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['applicant_email']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['applicant_name']); ?></td>
                        <td><?php echo htmlspecialchars($applicant['job_title']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($applicant['resume_path']); ?>" target="_blank">View Resume</a></td>
                        <td><?php echo htmlspecialchars($applicant['status']); ?></td>
                        <td>
                            <button class="proceed-button" data-id="<?php echo $applicant['id']; ?>">Proceed</button>
                            <button class="reject-button" data-id="<?php echo $applicant['id']; ?>">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;">No applicants found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="statusModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Update Status</h2>
        <input type="text" id="statusInput" placeholder="Enter new status (Interview, Contract, Hired)" />
        <button id="updateStatusButton" class="proceed-button">Update</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const proceedButtons = document.querySelectorAll('.proceed-button');
        const rejectButtons = document.querySelectorAll('.reject-button');
        const modal = document.getElementById("statusModal");
        const closeModal = document.querySelector(".close");
        const statusInput = document.getElementById("statusInput");
        const updateStatusButton = document.getElementById("updateStatusButton");

        let currentApplicantId = null;

        proceedButtons.forEach(button => {
            button.addEventListener('click', () => {
                const applicantId = button.getAttribute('data-id');
                currentApplicantId = applicantId;
                modal.style.display = "block";
            });
        });

        rejectButtons.forEach(button => {
            button.addEventListener('click', () => {
                const applicantId = button.getAttribute('data-id');
                updateStatus(applicantId, 'Rejected');
            });
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = "none";
            statusInput.value = "";
        });

        updateStatusButton.addEventListener('click', () => {
            const newStatus = statusInput.value;
            if (newStatus) {
                updateStatus(currentApplicantId, newStatus);
                modal.style.display = "none";
                statusInput.value = "";
            }
        });

        function updateStatus(applicantId, status) {
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: applicantId, status: status }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating status: ' + (data.error || 'Unknown error.'));
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>

<?php
$conn->close();
?>
