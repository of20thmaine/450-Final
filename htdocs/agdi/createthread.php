<?php
    session_start();

    $threadErrors = array();

    if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
        header('HTTP/1.0 404 Not Found', true, 404);
        die();
    }

    if (isset($_POST['threadName'])) {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

        $threadName = filter_var(trim($_POST['threadName']), FILTER_SANITIZE_STRING);

        if ($stmt = $con->prepare('INSERT INTO agtodi_threads (title, creatorId) VALUES (?, ?)')) {
            $stmt->bind_param('ss',$threadName, $_SESSION['id']);
            $stmt->execute();

            mysqli_close($con);
            header('Location: thread.php?thread=all');
            exit;

        } else {
            $threadErrors[] = 'We experienced an error, please try again.';
        }
    }

    $pageTitle = 'Agoti - Create Thread';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="argument">
    <div class="register">
        <h1>Create an Agtodi Thread</h1>
        <?php
        if ($threadErrors) {
            foreach ($threadErrors as $element) {
                echo '<p class="form-error">'."$element".'</p>';
            }
        }
        ?>
        <form action="createthread.php" method="post" autocomplete="off">
            <label for="name">
                <i class="fa fa-reply-all"></i>
            </label>
            <input type="text" name="threadName" placeholder="Enter thread title..." id="threadName" required>
            <input type="submit" value="Create">
        </form>
    </div>
</div>

<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
