<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

    if (isset($_GET['id'])) {
        $id = filter_var(trim($_GET['id']), FILTER_SANITIZE_STRING);
        $itsMe = false;

        if ($stmt = $con->prepare('SELECT firstName, lastName, tier FROM agtodi_users WHERE id = ?')) {
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($firstName, $lastName, $tier);
                $stmt->fetch();

                if (isset($_SESSION['id'])) {
                    if ($id == $_SESSION['id']) {
                        $itsMe = true;
                    }
                }
            } else {
                $stmt->close();
                mysqli_close($con);
                header('HTTP/1.0 404 Not Found', true, 404);
                die();
            }
        }

        if ($stmt = $con->prepare('SELECT a.post, DATE_FORMAT(a.creationDate,\'%m/%d/%Y\'), b.id, b.firstPostId,
                    (SELECT IFNULL(SUM(isLike),0) FROM agtodi_interactions WHERE postId = a.id) AS ags,
                    (SELECT IFNULL(SUM(isDislike),0) FROM agtodi_interactions WHERE postId = a.id) AS dis,
                    (SELECT IFNULL(COUNT(*),0) FROM agtodi_posts WHERE isReply = a.id) AS reps,
                    (SELECT title FROM agtodi_threads WHERE id = b.id) AS title
                    FROM agtodi_posts a JOIN agtodi_topics b ON a.topicId = b.id
                    WHERE a.creatorId = ?
                    ORDER BY a.creationDate DESC
                    LIMIT 20')) {
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $stmt->store_result();
        }
    } else {
        mysqli_close($con);
        header('HTTP/1.0 404 Not Found', true, 404);
        die();
    }

    $pageTitle = $firstName.' '.$lastName;
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="sidemenu">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
</div>

<div class="argument">
    <div class="prof-header">
        <?php if ($itsMe) { echo '<h2>Welcome '.$firstName.' '.$lastName.'!</h2><br>
                                    <h3>These are your recent posts.</h3>'; }
               else { echo '<h2>'.$firstName.' '.$lastName.'\'s Recent Posts</h2>'; }?>
    </div>
    <div class="card-area">
        <?php
        $stmt->bind_result($post, $date, $topicId, $fp, $ags, $dis, $reps, $title);
        while ($stmt->fetch()) {
            if ($ags > $dis) {
                $class = 'agree-card';
            } else if ($ags < $dis) {
                $class = 'disagree-card';
            } else if ($ags == 0 && $dis == 0) {
                $class = 'neutral-card';
            } else {
                $class = 'dispute-card';
            }
            echo "<a href=\"/agdi/topic.php?topic=$topicId&fp=$fp&title=$title\">
                  <div class=\"card $class\">
                    <p class=\"card-body o-flow-h\">$post</p>
                    <div class=\"card-footer\">
					    <div class=\"footer-left\">
					        <button class=\"foot-button ag-but\">$ags</button>
					        <button class=\"foot-button di-but\">$dis</button>
					        <button class=\"foot-button re-but\">$reps</button>
					    </div>
					    <div class=\"footer-right\">
                            <p class=\"card-datetime\">$date</p>
                            <a href=\"\" class=\"card-author\">$firstName $lastName</a>
						</div>
					</div>
                  </div></a>";
        }
        ?>
    </div>

</div>

<?php
    mysqli_close($con);
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');