<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

    if ($stmt = $con->prepare('SELECT id, creationDate, title FROM agtodi_threads ORDER BY title')) {
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
            $stmt->bind_result($id, $creationDate, $title);
            $toggle = true;
            while ($stmt->fetch()) {
                if ($toggle) {
                    echo '<a href="thread.php?thread='.$id.'&title='.$title.'"><div class="card" style="width: 44%; float:left"><p class="card-body">'.$title.'</p>
                      <div class="card-footer"><div class="footer-right"><p class="card-datetime">'.$creationDate.'</p>
                      </div></div></div></a>';
                } else {
                    echo '<a href="thread.php?thread='.$id.'&title='.$title.'"><div class="card" style="width: 44%; float:right"><p class="card-body">'.$title.'</p>
                      <div class="card-footer"><div class="footer-right"><p class="card-datetime">'.$creationDate.'</p>
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
