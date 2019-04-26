<?php
	session_start();

	$pageTitle = 'Agoti - Home';
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

    <div class="sidemenu">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
    </div>

<?php
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
