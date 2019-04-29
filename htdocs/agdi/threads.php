<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

    if ($stmt = $con->prepare('SELECT a.id, (SELECT IFNULL(COUNT(id), 0) FROM agtodi_topics WHERE threadId = a.id) AS topics, a.title FROM agtodi_threads a ORDER BY a.title LIMIT 100')) {
        $stmt->execute();
        $stmt->store_result();
    }

    $pageTitle = 'Agtodi - Topics';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="sidemenu">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
</div>

<div class="argument">
    <div class="threads-header">
        <h2>Agtodi Topics</h2>
        <?php
            if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
                echo '<button class="lg-button" onclick="window.location.href=\'createthread.php\'">Create</button>';
            }
        ?>
    </div>
    <div class="card-area">
        <?php
            $stmt->bind_result($id, $reps, $title);
            $toggle = true;
            while ($stmt->fetch()) {
                if ($toggle) {
                    echo '<a href="thread.php?thread='.$id.'&title='.$title.'"><div class="card l-c"><p class="card-body">'.$title.'</p>
                      <div class="card-footer"><div class="footer-left"><div class="count rep">Threads: '.$reps.'</div>
                      </div></div></div></a>';
                } else {
                    echo '<a href="thread.php?thread='.$id.'&title='.$title.'"><div class="card r-c"><p class="card-body">'.$title.'</p>
                      <div class="card-footer"><div class="footer-left"><div class="count rep">Threads: '.$reps.'</div>
                      </div></div></div></a>';
                }
                $toggle = !$toggle;
            }
            $stmt->close();
            mysqli_close($con);
        ?>
    </div>
</div>

<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
