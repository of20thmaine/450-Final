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
                                LIMIT 8";

    if ($stmt1 = $con->prepare($query)) {
    $stmt1->execute();
    $post_result = mysqli_stmt_get_result($stmt1);
    if ($post_result) {
        $all_posts1 = mysqli_fetch_all($post_result, MYSQLI_ASSOC);
    }
}
$stmt1->close();
?>
<div class="side-bar">
    <a href="/"><div class="menu-header-big"><i class="fa fa-home" aria-hidden="true"></i>Home</div></a>
    <a href="/agdi/threads.php"><div class="menu-header-big"><i class="fa fa-list" aria-hidden="true"></i>Topics</div></a>
    <a href="<?php
                if (isset($_SESSION['id'])) { echo '/profile.php?id='.$_SESSION['id']; } else { echo '/login.php?m=3'; }
             ?>"><div class="menu-header-big"><i class="fas fa-user" aria-hidden="true"></i>Profile</div></a>
    <?php
       if (!isset($_SESSION['id'])) {
        echo '<a href="/login.php"><div id="hidden-menu-1" class="menu-header-big"><i class="fas fa-sign-in-alt"></i>Login</div></a>
        <a href="/login.php"><div id="hidden-menu-2" class="menu-header-big"><i class="fas fa-user-tie"></i>Register</div></a>'; } ?>
    <div class="up-menu">
        <div class="menu-header dis"><i class="fab fa-hotjar" aria-hidden="true"></i>&emsp;Hot</div>
        <?php
        foreach($all_posts1 as $posts1) {
            echo '<a href="/agdi/topic.php?topic='.$posts1['topicId'].'&fp='.$posts1['fp'].'&title='.$posts1['title'].'">
                    <div class="mini-card"><div class="min-card-header">'.$posts1['title'].'</div>
                        <div class="mini-card-text">'.$posts1['post'].'</div>
                        <div class="footer-left">
                            <div class="count ags">'.$posts1['ags'].'</div>
                            <div class="count dis">'.$posts1['dis'].'</div>
                            <div class="count rep">'.$posts1['reps'].'</div>
                    </div></div></a>';

        }?>
    </div>
</div>