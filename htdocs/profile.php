<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

    if (isset($_GET['id'])) {
        $id = filter_var(trim($_GET['id']), FILTER_SANITIZE_STRING);

        // Display active topics, so get join of posts and topics sorted by post creationdate.
        if ($stmt = $con->prepare('SELECT ')) {

        }
    }

    $pageTitle = 'Agoti - User';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="argument">

</div>

<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');