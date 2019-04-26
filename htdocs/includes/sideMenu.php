<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');
    $query = "SELECT p.creationDate, p.post, q.id AS topicId, q.firstPostId AS fp,
                                (SELECT IFNULL(SUM(isLike),0) FROM agtodi_posts JOIN agtodi_interactions ON
                                 agtodi_interactions.postId = agtodi_posts.id WHERE agtodi_posts.topicId = q.id) AS ags,
                                 (SELECT IFNULL(SUM(isDislike),0) FROM agtodi_posts JOIN agtodi_interactions ON
                                  agtodi_interactions.postId = agtodi_posts.id WHERE agtodi_posts.topicId = q.id) AS dis,
                                (SELECT IFNULL(COUNT(*),0) FROM agtodi_posts WHERE topicId = q.id) AS reps,
                                (SELECT title FROM agtodi_threads WHERE id = q.threadId) AS title,
                                (SELECT SUM(ags + dis)) AS total
                                FROM agtodi_posts p JOIN agtodi_topics q ON p.id = q.firstPostId
                                ORDER BY total DESC
                                LIMIT 10";

    if ($stmt1 = $con->prepare($query)) {
        $stmt1->execute();
        $post_result = mysqli_stmt_get_result($stmt1);
        if ($post_result) {
            $all_posts = mysqli_fetch_all($post_result, MYSQLI_ASSOC);
        }
    }
    $stmt1->close();
?>
<div class="side-bar">
    <a href="/"><div class="menu-header-big"><i class="fa fa-home" aria-hidden="true"></i>&emsp;Home</div></a>
    <a href="/agdi/threads.php"><div class="menu-header-big"><i class="fa fa-list" aria-hidden="true"></i>&emsp;Topics</div></a>
    <a href="<?php
                if (isset($_SESSION['id'])) { echo '/profile.php?id='.$_SESSION['id']; } else { echo '/login.php'; }
             ?>"><div class="menu-header-big"><i class="fas fa-user" aria-hidden="true"></i>&emsp;Profile</div></a>
    <div class="up-menu">
        <div class="menu-header dis"><i class="fab fa-hotjar" aria-hidden="true"></i>&emsp;Hot</div>
        <?php
        foreach($all_posts as $post) {
            echo '<a href="/agdi/topic.php?topic='.$post['topicId'].'&fp='.$post['fp'].'&title='.$post['title'].'">
                    <div class="mini-card"><div class="min-card-header">'.$post['title'].'</div>
                        <div class="mini-card-text">'.$post['post'].'</div>
                        <div class="footer-left">
                            <div class="count ags">'.$post['ags'].'</div>
                            <div class="count dis">'.$post['dis'].'</div>
                            <div class="count rep">'.$post['reps'].'</div>
                    </div></div></a>';

        }?>
    </div>
</div>