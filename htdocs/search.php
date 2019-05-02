<?php
/*
 * search.php executes 3 separate queries; 1 searches for topics, 2 searches for
 */
    $all_threads = array();
    $all_posts = array();
    $all_users = array();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

    if (!empty($_GET['search'])) {
        $keyword = filter_var(trim($_GET['search']), FILTER_SANITIZE_STRING);

        if ($stmt = $con->prepare('SELECT t.id AS thread_id, t.creationDate AS thread_creation, t.title AS thread_title
                        FROM agtodi_threads t
                        WHERE t.title LIKE CONCAT(\'%\', ?, \'%\') 
                        ORDER BY title
                        LIMIT 10')) {
            $stmt->bind_param('s', $keyword);
            $stmt->execute();
            $thread_result = mysqli_stmt_get_result($stmt);
            if ($thread_result) {
                $all_threads = mysqli_fetch_all($thread_result, MYSQLI_ASSOC);
            }
        }
        if ($stmt = $con->prepare('SELECT p.id AS post_id, DATE_FORMAT(p.creationDate,\'%m/%d/%Y\') AS post_creation, p.post AS post_content, q.id AS topicId, q.firstPostId AS fp,
						(SELECT IFNULL(SUM(isLike),0) FROM agtodi_interactions WHERE postId = p.id) AS ags,
                        (SELECT IFNULL(SUM(isDislike),0) FROM agtodi_interactions WHERE postId = p.id) AS dis,
                        (SELECT IFNULL(COUNT(*),0) FROM agtodi_posts WHERE isReply = p.id) AS reps,
                        (SELECT title FROM agtodi_threads WHERE id = q.threadId) AS title,
                        b.firstName, b.lastName
                        FROM agtodi_posts p JOIN agtodi_topics q ON q.id = p.topicId JOIN
                        agtodi_users b ON b.id = p.creatorId
                        WHERE p.post LIKE CONCAT(\'%\', ?, \'%\') 
                        ORDER BY p.post LIMIT 10')) {
            $stmt->bind_param('s', $keyword);
            $stmt->execute();
            $post_result = mysqli_stmt_get_result($stmt);
            if ($post_result) {
                $all_posts = mysqli_fetch_all($post_result, MYSQLI_ASSOC);
            }
        }
        if ($stmt = $con->prepare('SELECT u.id AS user_id, u.firstName as user_first, u.lastName as user_last
                        FROM agtodi_users u
                        WHERE u.firstName LIKE CONCAT(\'%\', ?, \'%\')
                        OR u.lastName LIKE CONCAT(\'%\', ?, \'%\')
                        OR u.email LIKE CONCAT(\'%\', ?, \'%\') 
                        LIMIT 10')) {
            $stmt->bind_param('sss', $keyword, $keyword, $keyword);
            $stmt->execute();
            $user_result = mysqli_stmt_get_result($stmt);
            if ($user_result) {
                $all_users = mysqli_fetch_all($user_result, MYSQLI_ASSOC);
            }
        }
    }

    $pageTitle = 'Agtodi - Search';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

    <div class="sidemenu">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
    </div>

    <div class="argument">
        <div class="threads-header">
            <h2>Topics Containing <?php echo ' \''.$keyword.'\':'; ?></h2>
        </div>
        <div class="card-area">
             <?php
             $toggle = true;
             foreach($all_threads as $thread) {
                 if ($toggle) {
                     echo '<a href="/agdi/thread.php?thread='.$thread['thread_id'].'&title='.$thread['thread_title'].'">
                          <div class="card" style="width: 44%; float:left"><p class="card-body">'.$thread['thread_title'].'</p>
                          <div class="card-footer"><div class="footer-right"><p class="card-datetime">'.$thread['thread_creation'].'</p>
                          </div></div></div></a>';
                 } else {
                     echo '<a href="/agdi/thread.php?thread='.$thread['thread_id'].'&title='.$thread['thread_title'].'">
                        <div class="card" style="width: 44%; float:right"><p class="card-body">'.$thread['thread_title'].'</p>
                        <div class="card-footer"><div class="footer-right"><p class="card-datetime">'.$thread['thread_creation'].'</p>
                        </div></div></div></a>';
                 }
                 $toggle = !$toggle;
             } ?>
        </div>
    </div>
    <div class="argument">
        <div class="threads-header">
            <h2>Posts Containing <?php echo ' \''.$keyword.'\':'; ?></h2>
        </div>
        <div class="card-area">
            <?php
            foreach($all_posts as $post) {
                if ($post['ags'] > $post['dis']) {
                    $class = 'agree-card';
                } else if ($post['ags'] < $post['dis']) {
                    $class = 'disagree-card';
                } else if ($post['ags'] == 0 && $post['dis'] == 0) {
                    $class = 'neutral-card';
                } else {
                    $class = 'dispute-card';
                }
                echo "<a href=\"/agdi/topic.php?topic=".$post['topicId']."&fp=".$post['fp']."&title=".$post['title']."\">
                  <div class=\"card $class\">
                    <p class=\"card-body\">".$post['post_content']."</p>
                    <div class=\"card-footer\">
					    <div class=\"footer-left\">
					        <button class=\"foot-button ag-but\">".$post['ags']."</button>
					        <button class=\"foot-button di-but\">".$post['dis']."</button>
					        <button class=\"foot-button re-but\">".$post['reps']."</button>
					    </div>
					    <div class=\"footer-right\">
                            <p class=\"card-datetime\">".$post['post_creation']."</p>
                            <a href=\"\" class=\"card-author\">".$post['firstName'].' '.$post['lastName']."</a>
						</div>
					</div>
                  </div></a>";
            } ?>
        </div>
    </div>
    <div class="argument">
        <div class="threads-header">
            <h2>Users Containing <?php echo ' \''.$keyword.'\':'; ?></h2>
        </div>
        <div class="card-area">
            <?php foreach($all_users as $user) {
                $toggle = true;
                if ($toggle) {
                    echo '<a href="profile.php?id='.$user['user_id'].'">
                          <div class="card" style="width: 44%; float:left"><p class="card-body">'.$user['user_first']." ".$user['user_last'].'</p>
                            </div></a>';
                } else {
                    echo '<a href="profile.php?id='.$user['user_id'].'">
                        <div class="card" style="width: 44%; float:right"><p class="card-body">'.$user['user_first']." ".$user['user_last'].'</p>
                        </div></a>';
                }
                $toggle = !$toggle;
            } ?>
        </div>
    </div>

<?php
    mysqli_close($con);
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
