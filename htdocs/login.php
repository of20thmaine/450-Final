<?php
	session_start();

	if (isset($_POST['email'], $_POST['password'])) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

		if ($stmt = $con->prepare('SELECT id, password, firstName, lastName, tier FROM agtodi_users WHERE email = ?')) {
			$stmt->bind_param('s', $_POST['email']);
			$stmt->execute();
			$stmt->store_result();

			if ($stmt->num_rows > 0) {
				$stmt->bind_result($id, $password, $firstName, $lastName, $tier);
				$stmt->fetch();

				if (password_verify($_POST['password'], $password)) {
					session_regenerate_id();

					$_SESSION['loggedIn'] = TRUE;
					$_SESSION['email'] = $_POST['email'];
					$_SESSION['id'] = $id;
					$_SESSION['firstName'] = $firstName;
					$_SESSION['lastName'] = $lastName;
					$_SESSION['tier'] = $tier;

					// Fully logged in, redirect to profile.
				} else {
					// Bad password.
				}
			} else {
				// Bad username.
			}
			$stmt->close();
		}
	}

	$pageTitle = 'Agoti - Login';
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="argument">
	<div class="login">
		<h1>Login</h1>
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
	include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
