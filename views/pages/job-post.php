    
<link rel="stylesheet" href="./styles/job-post.css">
<div class="form-container">
    <h1>Post a job </h1>
    <form id="submissionForm" action="./database/migration/job-post-process.php" method="POST">
        <label for="job_title">Title:</label>
        <input type="text" id="job_title" name="job_title" required>

        <label for="job_short_desc">Short Description:</label>
        <input type="text" id="job_short_desc" name="job_short_desc" required>

        <label for="job_desc">Long Description:</label>
        <textarea id="job_desc" name="job_desc" required rows="8"></textarea>

        <button type="submit" id="submitBtn">Submit</button>
    </form>
</div>

<div id="successMessage" class="success-popup" style="display:none;">
    <p>Posted a job successfully!</p>
    <button onclick="closePopup()">OK</button>
</div>

<script src="./scripts/job-post.js"></script>
