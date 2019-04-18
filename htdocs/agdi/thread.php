<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

    if (isset($_GET['thread'], $_GET['title'])) {
        $threadId = filter_var(trim($_GET['thread']), FILTER_SANITIZE_STRING);
        $threadTitle = filter_var(trim($_GET['title']), FILTER_SANITIZE_STRING);

        if ($stmt = $con->prepare('SELECT a.firstPostId, b.post, b.creatorId, b.creationDate, b.likes, b.dislikes,
                c.firstName, c.lastName FROM agtodi_topics a JOIN agtodi_posts b ON b.id = a.firstPostId JOIN
                 agtodi_users c ON c.id = b.creatorId WHERE a.threadId = ?')) {
            $stmt->bind_param('s', $threadId);
            $stmt->execute();
            $stmt->store_result();

        }
    } else {
        header('Location: threads.php');
        exit;
    }

    $pageTitle = $threadTitle.' | Topics';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="argument">
    <div class="threads-header">
        <?php
        echo "<h2>$pageTitle</h2>";
        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
            echo '<button class="lg-button" onclick="window.location.href=\'createtopic.php?thread='.$threadId.
                '&title='.$threadTitle.'\'">Create</button>';
        }
        ?>
    </div>
    <div class="card-area">
    <?php
        $stmt->bind_result($fpId, $argument, $creatorId, $date, $ags, $dis, $firstName, $lastName);

        while ($stmt->fetch()) {
            if ($ags > $dis) {
                $class = 'agree-card';
            } else if ($ags < $dis) {
                $class = 'disagree-card';
            } else {
                $class = 'neutral-card';
            }
            echo "<div class=\"card $class\">
                    <p class=\"card-body\">$argument</p>
                    <div class=\"card-footer\">
					    <div class=\"footer-left\">
					        <div class=\"count ags\" style='padding-right: 20px'>$ags</div>
					        <div class=\"count dis\">$dis</div>
					    </div>
					    <div class=\"footer-right\">
                            <p class=\"card-datetime\">$date</p>
                            <a href=\"\" class=\"card-author\">$firstName $lastName</a>
						</div>
					</div>
                  </div>";
        }
    ?>
    </div>
</div>

<?php
    mysqli_close($con);
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
