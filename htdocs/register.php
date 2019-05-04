<?php
/*
 * register.php is a user registration script which uses recursive form handling to check for http post requests containing
 * the user registration data. Displays a form which is posted to this same script and checked for errors, which if found
 * are printed to the user over the form; otherwise the user is inserted into the database and redirected to login.
 */
    session_start();

    $registerErrors = array();

    if (isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'], $_POST['password2'])) {
        if (empty($_POST['firstName'])) {
            $registerErrors[] = 'First Name required!';
        }
        if (empty($_POST['lastName'])) {
            $registerErrors[] = 'Last Name required!';
        }
        if (empty($_POST['email'])) {
            $registerErrors[] = 'Email required!';
        }
        if (empty($_POST['password'])) {
            $registerErrors[] = 'Password required!';
        }
        if (empty($_POST['password2'])) {
            $registerErrors[] = 'Password re-entry required!';
        }

        if (!$registerErrors) {
            if (!filter_var(trim($_POST['firstName']), FILTER_SANITIZE_STRING) ||
                    strlen($_POST['firstName']) > 20 || strlen($_POST['firstName']) < 2 ) {
                $registerErrors[] = 'First name is not valid!';
            }
            if (!filter_var(trim($_POST['lastName']), FILTER_SANITIZE_STRING) ||
                    strlen($_POST['lastName']) > 20 || strlen($_POST['lastName']) < 2 ) {
                $registerErrors[] = 'Last name is not valid!';
            }
            if (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
                $registerErrors[] = 'Email is not valid!';
            }
            if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 7 ) {
                $registerErrors[] = 'Password must be between 8-20 characters long.';
            }
            if ($_POST['password'] != $_POST['password2']) {
                $registerErrors[] = 'Passwords must match.';
            }

            if (!$registerErrors) {
                require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');

                if ($stmt = $con->prepare('SELECT id FROM agtodi_users WHERE email = ?')) {
                    $stmt->bind_param('s', $_POST['email']);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        // Username already exists:
                        $registerErrors[] = 'A user with that email already exists.';
                    } else {
                        // Insert new account
                        if ($stmt = $con->prepare('INSERT INTO agtodi_users (email, password, firstName, lastName) VALUES (?, ?, ?, ?)')) {
                            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                            $stmt->bind_param('ssss', $_POST['email'], $password, $_POST['firstName'], $_POST['lastName']);
                            $stmt->execute();

                            mysqli_close($con);
                            header('Location: login.php?m=1');
                            exit;

                        } else {
                            $registerErrors[] = 'We experienced an error, please try again.';
                        }
                    }
                } else {
                    $registerErrors[] = 'We experienced an error, please try again.';
                }
            }
        }
    }

    $pageTitle = 'Agoti - Register';
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<div class="sidemenu">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/sideMenu.php'); ?>
</div>

<div class="argument">
    <div class="register">
        <h1>Register for Agtodi</h1>
        <?php
        if ($registerErrors) {
            foreach ($registerErrors as $element) {
                echo '<p class="form-error">'."$element".'</p>';
            }
        }
        ?>
        <form action="register.php" method="post" autocomplete="off">
            <label for="name">
                <i class="fa fa-user"></i>
            </label>
            <input type="text" name="firstName" placeholder="First Name" id="firstName" required>
            <label for="name">
                <i class="fa fa-user"></i>
            </label>
            <input type="text" name="lastName" placeholder="Last Name" id="lastName" required>
            <label for="email">
                <i class="fa fa-envelope"></i>
            </label>
            <input type="email" name="email" placeholder="Email" id="email" required>
            <label for="password">
                <i class="fa fa-lock"></i>
            </label>
            <input type="password" name="password" placeholder="Password (8-20 characters)" id="password" required>
            <label for="password">
                <i class="fa fa-lock"></i>
            </label>
            <input type="password" name="password2" placeholder="Re-enter password" id="password2" required>
            <input type="submit" value="Register">
        </form>
    </div>
</div>

<?php
    mysqli_close($con);
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
