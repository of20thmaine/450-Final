<?php
    session_start();
    $loggedIn = false;

    if (isset($_GET['topic'], $_GET['fp'], $_GET['title'])) {
        $topic = filter_var(trim($_GET['topic']), FILTER_SANITIZE_STRING);
        $fp = filter_var(trim($_GET['fp']), FILTER_SANITIZE_STRING);
        $topicTitle = filter_var(trim($_GET['title']), FILTER_SANITIZE_STRING);
        $posts = array();

        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

        if (isset($_SESSION['id'])) {
            $loggedIn = true;
        }

        if ($loggedIn) {
            if ($stmt = $con->prepare('SELECT a.id, a.creatorId, a.post, a.creationDate, 
                  (SELECT IFNULL(SUM(isLike),0) FROM agtodi_interactions WHERE a.id = postId) AS agrees, 
                  (SELECT IFNULL(SUM(isDislike),0) FROM agtodi_interactions WHERE a.id = postId) AS disagrees, 
                  a.isReply, b.firstName, b.lastName, b.tier, ((SELECT IFNULL(SUM(isDislike),0) FROM 
                  agtodi_interactions WHERE a.id = postId) + (SELECT IFNULL(SUM(isLike),0) FROM 
                  agtodi_interactions WHERE a.id = postId)) AS weight, IFNULL(c.isLike,0), IFNULL(c.isDislike,0), 
                  IFNULL(c.isTroll,0) FROM agtodi_posts a LEFT OUTER JOIN agtodi_interactions c ON a.id = c.postId 
                  AND c.creatorId = ?, agtodi_users b WHERE a.topicId = ? AND a.creatorId = b.id ORDER BY weight DESC')) {
                $stmt->bind_param('ss',$_SESSION['id'], $topic);
                $stmt->execute();
                $posts = $stmt->get_result()->fetch_all(MYSQLI_NUM);
                $tier = $_SESSION['tier'];
            }
        } else {
            if ($stmt = $con->prepare('SELECT a.id, a.creatorId, a.post, a.creationDate, 
                            (SELECT IFNULL(SUM(isLike),0) FROM agtodi_interactions WHERE a.id = postId) AS agrees, 
                            (SELECT IFNULL(SUM(isDislike),0) FROM agtodi_interactions WHERE a.id = postId)
                             AS disagrees, a.isReply, b.firstName, b.lastName, b.tier, 
                            ((SELECT IFNULL(SUM(isDislike),0) FROM agtodi_interactions WHERE a.id = postId)
                             + (SELECT IFNULL(SUM(isLike),0) FROM agtodi_interactions WHERE a.id = postId)) AS weight
                            FROM agtodi_posts a, agtodi_users b WHERE a.topicId = ? AND a.creatorId = b.id
                            ORDER BY weight DESC')) {
                $stmt->bind_param('s', $topic);
                $stmt->execute();
                $posts = $stmt->get_result()->fetch_all(MYSQLI_NUM);
                $tier = -1;
            }
        }
        $fp_index = 0;
        $nested = array();
        for ($i = 0; $i < count($posts); ++$i) {
            if ($posts[$i][0] == $fp) {
                $fp_index = $i;
            }
            if ($posts[$i][6] != null) {
                if (array_key_exists($posts[$i][6], $nested)) {
                    $nested[$posts[$i][6]][] = $i;
                } else {
                    $nested[$posts[$i][6]] = array($i);
                }
            }
        }
    }

    function getCardClass($ags, $dis) {
        if ($ags > $dis) {
            return 'agree-card';
        } else if ($ags < $dis) {
            return 'disagree-card';
        } else if ($ags == 0 && $dis == 0) {
            return 'neutral-card';
        } else {
            return 'dispute-card';
        }
    }

    function printCard($i, $posts, $nested, $topic, $tier, $fp, $topicTitle, $replyTier) {
        if (isset($nested) && array_key_exists($posts[$i][0], $nested)) {
            $count = count($nested[$posts[$i][0]]);
        } else if ($posts[$i][0] == $fp) {
            $count = count($posts)-1;
        } else {
            $count = 0;
        }
        echo '<div id="c'.$posts[$i][0].'" class="card '.getCardClass($posts[$i][4], $posts[$i][5]).'">
                <p class="card-body">'.$posts[$i][2].'</p>
                <div class="card-footer">
                    <div class="footer-left">
                         <form action="interaction.php" method="post">
                            <input type="hidden" name="location" value="topic.php?topic='.$topic.'&fp='.$fp.'&title='.$topicTitle.'">
                            <input type="hidden" name="fp" value="'.$fp.'">
                            <button class="foot-button ag-but';
                    if ($tier != -1 && $posts[$i][11] == 1) {echo '-sel';}
                    echo '" name="agree" type="submit" value="'.$posts[$i][0].'">Ag</button>
                            <div class="count ags">'.$posts[$i][4].'</div>
                            <button class="foot-button di-but';
                    if ($tier != -1 && $posts[$i][12] == 1) {echo '-sel';}
                    echo '" name="disagree" type="submit" value="'.$posts[$i][0].'">Di</button>
                            <div class="count dis">'.$posts[$i][5].'</div>';
            if ($replyTier < 2) {
                echo ' <button class="foot-button re-but" onclick="displayReplyBox(\'c'.$posts[$i][0].'\','.$fp.',
                            \'topic.php?topic='.$topic.'&fp='.$fp.'&title='.$topicTitle.'\','.$topic.'); return false;">Reply</button>
                        <div class="count rep">'.$count.'</div>';
            }
                echo    '</form>
                     </div>';
        if ($tier >= $posts[$i][9] && $posts[$i][1] != $_SESSION['id']) {
            echo '<form class="tr-form" action="interaction.php" method="post">
                     <input type="hidden" name="location" value="topic.php?topic='.$topic.'&fp='.$fp.'&title='.$topicTitle.'">
                     <input type="hidden" name="user" value="'.$posts[$i][1].'">
                     <button class="foot-button tr-but';
            if ($tier != -1 && $posts[$i][13] == 1) {echo '-sel';}
            echo '" name="troll" type="submit" value="'.$posts[$i][0].'">Troll</button>
                   </form>';
        }
        echo '<div class="footer-right">
                    <p class="card-datetime">'.$posts[$i][3].'</p>
                    <a href="/profile.php?id='.$posts[$i][1].'" class="card-author">
                        '.$posts[$i][7].' '.$posts[$i][8].'</a>
                </div>
            </div>
            <div class="reply-area" style="display:none;"></div>
        </div>';
    }

    $pageTitle = $posts[$fp_index][2];
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>
<div class="sidemenu">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
</div>

<div class="argument">
    <div class="threads-header">
        <h2><?php echo $topicTitle; ?>/</h2>
    </div>
    <div class="card-area">
        <?php printCard($fp_index, $posts, $nested, $topic, $tier, $fp, $topicTitle, 0); ?>
        <div class="tier">
        <?php
            $tierCount = 0;
            for ($i = 0; $i < count($posts); ++$i) {
                if ($posts[$i][0] == $fp) {
                    continue;
                }
                if (array_key_exists($posts[$i][0], $nested) && $posts[$i][6] == null) {
                    printCard($i, $posts, $nested, $topic, $tier, $fp, $topicTitle, $tierCount);
                    $tierCount++;
                    echo '<div class="tier">';
                    for ($j = 0; $j < count($nested[$posts[$i][0]]); ++$j) {
                        if (array_key_exists($posts[$nested[$posts[$i][0]][$j]][0], $nested)) {
                            printCard($nested[$posts[$i][0]][$j], $posts, $nested, $topic, $tier, $fp, $topicTitle, $tierCount);
                            $tierCount++;
                            echo '<div class="tier">';
                            for ($k = 0; $k < count($nested[$posts[$nested[$posts[$i][0]][$j]][0]]); ++$k) {
                                printCard($nested[$posts[$nested[$posts[$i][0]][$j]][0]][$k], $posts, $nested, $topic, $tier, $fp, $topicTitle, $tierCount);
                            }
                            echo '</div>';
                            $tierCount--;
                        } else {
                            printCard($nested[$posts[$i][0]][$j], $posts, $nested, $topic, $tier, $fp, $topicTitle, $tierCount);
                        }
                    }

                    echo '</div>';
                    $tierCount--;
                } else if ($posts[$i][6] != null) {
                    continue;
                } else {
                    printCard($i, $posts, $nested, $topic, $tier, $fp, $topicTitle, $tierCount);
                }
            }
            mysqli_close($con);
        ?>
        </div>
    </div>
</div>


<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
