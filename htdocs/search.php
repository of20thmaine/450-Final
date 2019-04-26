<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
    if(!empty($_GET['search'])) {
        $keyword = $_GET['search'];
        echo "testing";
        echo $keyword;
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');
        //thread search
        $thread_query = "SELECT t.id AS thread_id, t.creationDate AS thread_creation, t.title AS thread_title
                    FROM agtodi_threads T
                    WHERE t.title LIKE CONCAT('%', ?, '%')
                    ORDER BY title
    ";
        $thread_stmt = mysqli_prepare($con, $thread_query);
        mysqli_stmt_bind_param($thread_stmt, "s", $keyword);
        mysqli_stmt_execute($thread_stmt);
        $thread_result = mysqli_stmt_get_result($thread_stmt);
        if ($thread_result) {
            $all_threads = mysqli_fetch_all($thread_result, MYSQLI_ASSOC);
            $num_rows = mysqli_num_rows($thread_result);
        } else {
            echo "<h2>No threads found.</h2>";
            echo "<h3>Please try a different keyword.</h3>";
            exit;
        }

    //post search
        $post_query = "SELECT p.id AS post_id, p.creationDate AS post_creation, p.post AS post_content
                        FROM agtodi_posts p
                        WHERE p.post LIKE CONCAT('%', ?, '%')
                        ORDER BY p.post
        ";
        $post_stmt = mysqli_prepare($con, $post_query);
        mysqli_stmt_bind_param($post_stmt, "s", $keyword);
        mysqli_stmt_execute($post_stmt);
        $post_result = mysqli_stmt_get_result($post_stmt);
        if($post_result) {
                $all_posts = mysqli_fetch_all($post_result, MYSQLI_ASSOC);
                $num_rows = mysqli_num_rows($post_result);
        }else {
            echo "<h2>No posts found.</h2>";
            echo "<h3>Pleast try a different keyword.</h3>";
            exit;
        }

        //user search
        $user_query = "SELECT u.id AS user_id, u.firstName as user_first, u.lastName as user_last, u.email as user_email, u.tier as user_tier
            FROM agtodi_users u
            WHERE u.firstName LIKE CONCAT('%', ?, '%')
            OR u.lastName LIKE CONCAT('%', ?, '%')
            OR u.email LIKE CONCAT('%', ?, '%')
        ";
        $user_stmt = mysqli_prepare($con, $user_query);
        mysqli_stmt_bind_param($user_stmt, "sss", $keyword, $keyword, $keyword);
        mysqli_stmt_execute($user_stmt);
        $user_result = mysqli_stmt_get_result($user_stmt);
        if($user_result) {
            $all_users = mysqli_fetch_all($user_result, MYSQLI_ASSOC);
            $num_rows = mysqli_num_rows($user_result);
        }else {
            echo "<h2>No posts found.</h2>";
            echo "<h3>Pleast try a different keyword.</h3>";
            exit;
        }
        mysqli_close($con);
    }else {
        echo "You have reached this page in error";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>AGTODI</title>
	<meta charset ="utf-8">
</head>
<body>
	<h1>Testing Search Query:</h1>
    <h2>Threads with Keyword:</h2>
	<table>
		<tr>
			<th>Thread Id</th>
			<th>Thread Creation Date</th>
			<th>Thread Title</th>
		</tr>
		<?php foreach($all_threads as $thread) {
			echo "<tr>";
			echo "<td>".$thread['thread_id']."</td>";
			echo "<td>".$thread['thread_creation']."</td>";
			echo "<td>".$thread['thread_title']."</td>";
			echo "</tr>";
		} ?>
	</table>
	<br>
    <h2>Posts with Keyword:</h2>
    <table>
        <tr>
            <th>Post Id</th>
            <th>Post Creation Date</th>
            <th>Post Content</th>
        </tr>
        <?php foreach($all_posts as $post) {
            echo "<tr>";
            echo "<td>".$post['post_id']."</td>";
            echo "<td>".$post['post_creation']."</td>";
            echo "<td>".$post['post_content']."</td>";
            echo "</tr>";
        } ?>
    </table>
    <br>
    <h2>Users with Keyword:</h2>
    <table>
        <tr>
            <th>User Id</th>
            <th>User Name</th>
            <th>User Email</th>
            <th>User Tier</th>
        </tr>
        <?php foreach($all_users as $user) {
            echo "<tr>";
            echo "<td>".$user['user_id']."</td>";
            echo "<td>".$user['user_first']." ".$user['user_last']."</td>";
            echo "<td>".$user['user_email']."</td>";
            echo "<td>".$user['user_tier']."</td>";
            echo "</tr>";
        } ?>
    </table>
    <br>
	<h3><a href="../agdi/threads.php">Back to Home</a></h3>
</body>
</html>
