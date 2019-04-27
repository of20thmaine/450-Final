<?php
    session_start();

    if (!isset($_SESSION['id'])) {
        header('Location: /login.php?m=2');
        exit;
    }

    if ((isset($_POST['agree']) || isset($_POST['disagree'])) && isset($_POST['location'])) {
        $agree = false;
        if (isset($_POST['agree'])) {
            $postId = filter_var(trim($_POST['agree']), FILTER_SANITIZE_STRING);
            $agree = true;
        } else if (isset($_POST['disagree'])) {
            $postId = filter_var(trim($_POST['disagree']), FILTER_SANITIZE_STRING);
        }
        $redirect = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);

        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

        if ($stmt = $con->prepare('SELECT IFNULL(isLike, 0), IFNULL(isDislike,0) FROM agtodi_interactions WHERE postId = ? AND creatorId = ?')) {
            $stmt->bind_param('ss',$postId, $_SESSION['id']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($likes, $dislikes);

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                if ($agree) {
                    if ($likes == 1) {
                        $sql = 'DELETE FROM agtodi_interactions WHERE postId = ? AND creatorId = ?';
                    } else {
                        $sql = 'UPDATE agtodi_interactions SET isLike = 1, isDislike = 0, isTroll = 0 WHERE postId = ? AND creatorId = ?';
                    }
                } else {
                    if ($dislikes == 1) {
                        $sql = 'DELETE FROM agtodi_interactions WHERE postId = ? AND creatorId = ?';
                    } else {
                        $sql = 'UPDATE agtodi_interactions SET isLike = 0, isDislike = 1, isTroll = 0 WHERE postId = ? AND creatorId = ?';
                    }
                }
            } else {
                if ($agree) {
                    $sql = 'INSERT INTO agtodi_interactions (postId, creatorId, isLike) VALUES (?, ?, 1)';
                } else {
                    $sql = 'INSERT INTO agtodi_interactions (postId, creatorId, isDislike) VALUES (?, ?, 1)';
                }
            }
            if ($stmt = $con->prepare($sql)) {
                $stmt->bind_param('ss',$postId, $_SESSION['id']);
                $stmt->execute();
            }
        }
        mysqli_close($con);
        header('Location: '.$redirect);
        exit;
    }

    if (isset($_POST['troll'], $_POST['user'], $_POST['location'])) {
        $postId = filter_var(trim($_POST['troll']), FILTER_SANITIZE_STRING);
        $user = filter_var(trim($_POST['user']), FILTER_SANITIZE_STRING);
        $redirect = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);
        $timeToLook = false;

        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

        if ($stmt = $con->prepare('SELECT IFNULL(isTroll,0) FROM agtodi_interactions WHERE postId = ? AND creatorId = ?')) {
            $stmt->bind_param('ss',$postId, $_SESSION['id']);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($trolls);

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                if ($trolls == 1) {
                    $sql = 'DELETE FROM agtodi_interactions WHERE postId = ? AND creatorId = ?';
                } else {
                    $sql = 'UPDATE agtodi_interactions SET isLike = 0, isDislike = 0, isTroll = 1 WHERE postId = ? AND creatorId = ?';
                    $timeToLook = true;
                }
            } else {
                $sql = 'INSERT INTO agtodi_interactions (postId, creatorId, isTroll) VALUES (?, ?, 1)';
                $timeToLook = true;
            }
            if ($stmt = $con->prepare($sql)) {
                $stmt->bind_param('ss',$postId, $_SESSION['id']);
                $stmt->execute();
            }

            if ($timeToLook) {
                if ($stmt = $con->prepare('SELECT SUM(isTroll) FROM agtodi_interactions WHERE creatorId = ?')) {
                    $stmt->bind_param('s',$user);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($trolls);
                    $stmt->fetch();

                    if ($trolls >= 3) {
                        if ($stmt = $con->prepare('DELETE FROM agtodi_posts WHERE id = ?')) {
                            $stmt->bind_param('s',$postId);
                            $stmt->execute();
                        }
                    }
                }
            }
        }
        mysqli_close($con);
        header('Location: '.$redirect);
        exit;
    }

    if (isset($_POST['postId'], $_POST['reply'], $_POST['location'], $_POST['fp'], $_POST['topic'])) {
        $postId = filter_var(trim($_POST['postId']), FILTER_SANITIZE_STRING);
        $reply = filter_var(htmlspecialchars($_POST['reply']), FILTER_SANITIZE_STRING);
        $redirect = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);
        $fp = filter_var(trim($_POST['fp']), FILTER_SANITIZE_STRING);
        $topic = filter_var(trim($_POST['topic']), FILTER_SANITIZE_STRING);

        if ($postId == $fp) {
            $postId = null;
        }

        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

        if ($stmt = $con->prepare('INSERT INTO agtodi_posts (creatorId, topicId, isReply, post) VALUES (?, ?, ?, ?)')) {
            $stmt->bind_param('ssss',$_SESSION['id'], $topic, $postId, $reply);
            $stmt->execute();
        }
        mysqli_close($con);
        header('Location: '.$redirect);
        exit;
    }

header('HTTP/1.0 404 Not Found', true, 404);
die();
