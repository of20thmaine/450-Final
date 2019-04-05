<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $pageTitle; ?></title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<link rel="stylesheet" href="./css/app.css">
</head>
<body>
	<div class="topnav">
		<div class="topnav-left">
			<a href="index.php"><p id="logo-l">agt</p><p id="logo-r">odi</p></a>
		</div>
		<div class="topnav-search">
            <form action="search.php" method="post">
                <label for="search">
                    <i class="fa fa-search"></i>
                </label>
                <input type="text" name="search" placeholder="Search agtodi...">
            </form>
		</div>
		<div class="topnav-right">
			<button onclick="window.location.href='login.php'">Login</button>
			<button onclick="window.location.href='register.php'">Register</button>
		</div>
	</div>
	<div class="content">
