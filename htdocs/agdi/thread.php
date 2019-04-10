<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');
    $isThread = false;

    if (isset($_GET['thread'])) {
        $threadId = filter_var(trim($_POST['thread']), FILTER_SANITIZE_STRING);

        if ($stmt = $con->prepare('SELECT title FROM agtodi_threads WHERE threadId = ?')) {
            $stmt->bind_param('s', $threadId);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($title);
                $stmt->fetch();
                $isThread = true;
            }

            $stmt->free_result();

            if ($isThread && $stmt = $con->prepare('SELECT id, firstPostId, creationDate, title FROM agtodi_topics WHERE threadId = ? ORDER BY title')) {
                $stmt->bind_param('s', $threadId);
                $stmt->execute();
                $stmt->store_result();
            }
        }
    }

    if (!$isThread) {
        $title = 'Threads';
    }

    $pageTitle = 'Agoti - '.$title;
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="argument">
    <div class="argument-header">
        <h2>ag:di//<?php echo $title; ?></h2>
    </div>
    <div class="card-area">
    <?php
         if ($isThread) {
             $stmt->bind_result($id, $firstPostId, $creationDate, $title);

             while ($stmt->fetch()) {

             }
         } else {
             if ($stmt = $con->prepare('SELECT title, id FROM agtodi_threads')) {
                 $stmt->execute();
                 $stmt->store_result();
                 $stmt->bind_result($title, $id);
             }

             echo '<ul>';
             while ($stmt->fetch()) {
                 echo '<li><a href="thread.php?thread='.$id.'">'.$title.'</a></li>';
             }

             echo '</ul>';
         }
    ?>
    </div>
</div>

<?php
    mysqli_close($con);
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
