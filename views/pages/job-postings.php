<?php
include $_SERVER['DOCUMENT_ROOT'] . '/paradise-app/database/connection.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM job_application";
$result = $conn->query($query);
?>

<link rel="stylesheet" href="./styles/job-postings.css">
<main>
    <div class="job-postings-container">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $jobId = htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8');
                $jobTitle = htmlspecialchars($row["job_title"], ENT_QUOTES, 'UTF-8');
                $jobShortDesc = htmlspecialchars($row["job_short_desc"], ENT_QUOTES, 'UTF-8');
                $jobDate = htmlspecialchars($row["date"], ENT_QUOTES, 'UTF-8');

                echo '<div class="card-forms">';
                echo '<a href="./index.php?page=job-details&id=' . $jobId . '" class="card">';
                echo '<h2>' . $jobTitle . '</h2>';
                echo '<p>' . $jobShortDesc . '</p>';
                echo '<p class="date">Date: ' . $jobDate . '</p>';
                echo '</a>';
                echo '</div>';
                
            }
        } else {
            echo "<p>No job postings available at this time.</p>";
        }

        $conn->close();
        ?>
    </div>
</main>
