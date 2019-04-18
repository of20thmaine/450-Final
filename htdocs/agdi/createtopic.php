<?php
    session_start();

    function killScript() {
        header('HTTP/1.0 404 Not Found', true, 404);
        die();
    }

    $topicErrors = array();

    if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
        killScript();
    }

    if (!isset($_GET['thread'], $_GET['title'])) {
        killScript();
    }

    $threadId = filter_var(trim($_GET['thread']), FILTER_SANITIZE_STRING);
    $threadTitle = filter_var(trim($_GET['title']), FILTER_SANITIZE_STRING);

    if (isset($_POST['threadId'], $_POST['argument'])) {
        $argument = filter_var(trim(htmlspecialchars($_GET['thread'])), FILTER_SANITIZE_STRING);
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

        $con->begin_transaction();
        $stmt = $con->prepare('INSERT INTO agtodi_topics (threadId) VALUES (?)');
        $stmt->bind_param('s',$threadId);

        if ($stmt->execute()) {
                $topicID = mysqli_insert_id($con);
                $stmt = $con->prepare('INSERT INTO agtodi_posts (creatorId, topicId, post) VALUES (?, ?, ?)');
                $stmt->bind_param('sss',$_SESSION['id'], $topicID, $argument);
            if ($stmt->execute()) {
                $postID = mysqli_insert_id($con);
                $stmt = $con->prepare('UPDATE agtodi_topics SET firstPostId = ? WHERE id = ?');
                $stmt->bind_param('ss', $postID, $topicID);
                if ($stmt->execute()) {
                    mysqli_close($con);
                    header('Location: threads.php');
                    exit;
                }
            }
        }
        $con->rollback();
        mysqli_close($con);
        $topicErrors[] = 'Connection failed.';
    }

    $pageTitle = 'Agoti - Create Topic | '.$threadTitle;
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="argument">
    <div class="register">
        <h1>Create Topic:<br><?php echo $threadTitle; ?></h1>
        <?php
            if ($topicErrors) {
                foreach ($topicErrors as $element) {
                    echo '<p class="form-error">'."$element".'</p>';
                }
            }
        ?>
        <form action="createtopic.php?thread=<?php echo $threadId.'&title='.$threadTitle; ?>" method="post" autocomplete="off">
            <input type="hidden" id="threadId" name="threadId" value="<?php echo $threadId; ?>">
            <textarea id="argument" class="text" name="argument" placeholder="Enter argument text..."></textarea>
            <input type="submit" value="Create">
        </form>
    </div>
</div>

<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');