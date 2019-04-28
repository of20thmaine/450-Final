<?php
	session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

    if ($stmt = $con->prepare('SELECT DATE_FORMAT(p.creationDate,\'%m/%d/%Y\'), p.post, q.id AS topicId, q.firstPostId AS fp,
                        (SELECT IFNULL(SUM(isLike),0) FROM agtodi_posts JOIN agtodi_interactions ON
                         agtodi_interactions.postId = agtodi_posts.id WHERE agtodi_posts.topicId = q.id) AS ags,
                         (SELECT IFNULL(SUM(isDislike),0) FROM agtodi_posts JOIN agtodi_interactions ON
                          agtodi_interactions.postId = agtodi_posts.id WHERE agtodi_posts.topicId = q.id) AS dis,
                        (SELECT IFNULL(COUNT(*),0) FROM agtodi_posts WHERE topicId = q.id) AS reps,
                        (SELECT title FROM agtodi_threads WHERE id = q.threadId) AS title, u.id, u.firstName, u.lastName,
                        (SELECT SUM(ags + dis)) AS total
                        FROM agtodi_posts p JOIN agtodi_topics q ON p.id = q.firstPostId JOIN agtodi_users u ON u.id = p.creatorId
                        ORDER BY total DESC
                        LIMIT 20')) {
        $stmt->execute();
        $stmt->store_result();
    }

	$pageTitle = 'Agoti - Home';
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

    <div class="sidemenu">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
    </div>

    <div class="argument">
        <div class="prof-header">
            <h2><i class="fas fa-chart-line"></i>Today's Trending Threads:</h2>
        </div>
        <div class="card-area">
            <?php
            $stmt->bind_result($date,$argument, $id, $fpId, $ags, $dis, $replies, $title, $creatorId, $firstName, $lastName, $total);
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
                echo "<a href=\"/agdi/topic.php?topic=$id&fp=$fpId&title=$title\">
                  <div class=\"card $class\">
                    <p class=\"card-body o-flow-h\">$argument</p>
                    <div class=\"card-footer\">
					    <div class=\"footer-left\">
					        <button class=\"foot-button ag-but\">$ags</button>
					        <button class=\"foot-button di-but\">$dis</button>
					        <button class=\"foot-button re-but\">$replies</button>
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

    <div class="right-area">
        <img src="./img/banner-1.png">
    </div>

<?php
    mysqli_close($con);
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
