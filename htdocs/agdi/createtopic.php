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

    if (!isset($_GET['threadId'], $_GET['thread'])) {
        killScript();
    }

    $threadTitle = filter_var(trim($_GET['thread']), FILTER_SANITIZE_STRING);
    $threadId = filter_var(trim($_GET['threadId']), FILTER_SANITIZE_STRING);



    $pageTitle = 'Agoti - Create Topic | '.$threadTitle;
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="argument">
    <div class="register">
        <h1>Create Topic: <?php echo $threadTitle; ?></h1>
        <?php
            if ($topicErrors) {
                foreach ($topicErrors as $element) {
                    echo '<p class="form-error">'."$element".'</p>';
                }
            }
        ?>
        <form action="createtopic.php" method="post" autocomplete="off">
            <input type="hidden" id="threadId" name="threadId" value="<?php echo $threadId; ?>">
            <textarea id="argument" class="text" name="argument" placeholder="Enter argument text..."></textarea>
            <input type="submit" value="Create">
        </form>
    </div>
</div>

<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');