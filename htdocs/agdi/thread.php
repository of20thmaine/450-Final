<?php
    session_start();

    if (isset($_GET['thread'])) {


        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');
    } else {

    }



    $pageTitle = 'Agoti - Thread';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>


<div class="argument">

</div>


<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
