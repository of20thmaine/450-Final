<?php
/*
 * login.php authenticates users by checking if the input parameters from http Post are valid, if so it redirects users
 * to their profile and sets the session. If not they are given an error. If no Post parameters are given the script
 * displays the login form normally.
 */
	session_start();

    $loginErrors = array();

	if (isset($_POST['email'], $_POST['password'])) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

		// Retrieve user given post paramaters of email and password.
		if ($stmt = $con->prepare('SELECT id, password, firstName, lastName, tier, isAdmin FROM agtodi_users WHERE email = ?')) {
			$stmt->bind_param('s', $_POST['email']);
			$stmt->execute();
			$stmt->store_result();
            // Authenticate the inputted password.
			if ($stmt->num_rows > 0) {
				$stmt->bind_result($id, $password, $firstName, $lastName, $tier, $isAdmin);
				$stmt->fetch();

				if (password_verify($_POST['password'], $password)) {
					session_regenerate_id();
                    // Set the session variables.
					$_SESSION['loggedIn'] = TRUE;
					$_SESSION['email'] = $_POST['email'];
					$_SESSION['id'] = $id;
					$_SESSION['firstName'] = $firstName;
					$_SESSION['lastName'] = $lastName;
					$_SESSION['tier'] = $tier;
					$_SESSION['isAdmin'] = $isAdmin;

                    mysqli_close($con);
                    header('Location: profile.php?id='.$id);
                    exit;

				} else {
                    $loginErrors[] = 'Invalid password.';
				}
			} else {
                $loginErrors[] = 'Invalid username.';
			}
			$stmt->close();
		}
	}

	$pageTitle = 'Agoti - Login';
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="sidemenu">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
</div>

<div class="argument">
	<div class="login">
		<h1>Welcome to Agtodi!</h1>
        <?php
             // Prints errors in login process.
            if ($loginErrors) {
                foreach ($loginErrors as $element) {
                    echo '<p class="form-error">'."$element".'</p>';
                }
            }
            // Print special messages if redirect caused this script to load.
            if (isset($_GET['m'])) {
                $m = filter_var(trim($_GET['m']), FILTER_SANITIZE_STRING);
                if ($m == 1) {
                    echo '<p class="form-message">You successfully registered! Please sign in.</p>';
                } else if ($m == 2) {
                    echo '<p class="form-message">You must login to participate.</p>';
                } else if ($m == 3) {
                    echo '<p class="form-message">Login to view your profile.</p>';
                }
            }
        ?>
		<form action="login.php" method="post">
			<label for="username">
				<i class="fa fa-user"></i>
			</label>
			<input type="text" name="email" placeholder="Email" id="email" required>
			<label for="password">
				<i class="fa fa-lock"></i>
			</label>
			<input type="password" name="password" placeholder="Password" id="password" required>
			<input type="submit" value="Login">
		</form>
	</div>
</div>

<?php
    mysqli_close($con);
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
