<?php
    session_start();

    if (isset($_SESSION['id'])) {
        $_SESSION = array();
        session_destroy();

        header('Location: index.php');
        exit();
    } else {
        header('Location: index.php');
        exit();
    }
