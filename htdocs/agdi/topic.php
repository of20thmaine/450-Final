<?php
    session_start();

    if (isset($_GET['topic_id'])) {
        $topic = $_GET['topic_id'];

        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

//
//        if ($stmt = $con->prepare('SELECT a.id, a.creatorId, a.post, a.creationDate, a.likes, a.dislikes, a.trolls,
//              a.isReply, b.firstName, b.lastName, c.isLike, c.isDislike, c.isTroll FROM agtodi_posts a WHERE topicId = ?
//              JOIN agtodi_users b ON a.creatorId = b.id JOIN agtodi_interactions c ON c.creatorId = ?')) {
//            echo 'x';
//        }




    } else {

    }

    $pageTitle = 'Agoti - Topic';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="sidemenu">

</div>

<div class="argument">
    <div class="argument-header">
        <h2>ag:di//Example Topic</h2>
    </div>
    <div class="card-area">
        <div class="card header-card">

        </div>





    </div>
</div>

<div class="right-area">
    <img src="./img/banner-1.png">
</div>

<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
