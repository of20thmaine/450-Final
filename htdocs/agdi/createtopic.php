<?php
/*
 * Allows admins to create agtodi topics, uses recursive form handling.
 */
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
        $argument = filter_var(htmlspecialchars($_POST['argument']), FILTER_SANITIZE_STRING);
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

        // Mysql transaction in php, we want the whole thing to work or none of it.
        $con->begin_transaction();
        $stmt1 = $con->prepare('INSERT INTO agtodi_topics (threadId) VALUES (?)');
        $stmt1->bind_param('s',$threadId);

        if ($stmt1->execute()) {
                $topicID = mysqli_insert_id($con);
                $stmt2 = $con->prepare('INSERT INTO agtodi_posts (creatorId, topicId, post) VALUES (?, ?, ?)');
                $stmt2->bind_param('sss',$_SESSION['id'], $topicID, $argument);
            if ($stmt2->execute()) {
                $postID = mysqli_insert_id($con);
                $stmt3 = $con->prepare('UPDATE agtodi_topics SET firstPostId = ? WHERE id = ?');
                $stmt3->bind_param('ss', $postID, $topicID);
                if ($stmt3->execute()) {
                    $topicErrors[] = $topicID.'  '.$postID.'  '.$argument;
                    $con->commit();
                    mysqli_close($con);
                    header('Location: thread.php?thread='.$threadId.'&title='.$threadTitle);
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

<div class="sidemenu">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
</div>

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