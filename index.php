<?php 
    include './views/components/header.php';
    include './views/components/navigation.php';
?>
<body class="light-mode"> 
    <main class="main-content">
        <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            switch ($page) {
                case 'job-post':
                    include './database/migration/job-post-process.php';
                    break;
                case 'job-postings':
                    include './views/pages/job-postings.php';
                    break;
                case 'job-details':
                    include './views/pages/job-details.php';
                    break;
                case 'apply-job':
                    include './views/pages/job-apply.php';
                    break;
                default:
                    include './views/pages/job-post.php';
                    break;
            }
        ?>
    </main>
    <script src="./scripts/index.js"> </script>
</body>
