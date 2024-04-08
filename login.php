<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
		exit;
	} else {
		if (!isset($_POST['email'], $_POST['password']) || empty($_POST['email']) || empty($_POST['password'])) {
			// Could not get the data that should have been sent, or fields are empty
			exit('Please fill both the email and password fields!');
		}

		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			exit('Email is not valid!');
		}
		// connect DB
		require_once ("includes/connectdb.php");
		try {
			//Query DB to find the matching email/password
			//using prepare/bindparameter to prevent SQL injection.
			$stat = $db->prepare('SELECT * FROM users WHERE email = ?');
			$stat->execute(array($_POST['email']));

			// fetch the result row and check 
			if ($stat->rowCount() > 0) {  // matching email
				$row = $stat->fetch();

				if (password_verify($_POST['password'], $row['password'])) { //matching password

					session_start();
					$_SESSION["username"] = $row['username'];
					$_SESSION["uid"] = $row['uid'];
					header("Location:index.php");
					exit();

				} else {
					echo "<p style='color:red'>Error logging in, password does not match </p>";
				}
			} else {
				//else display an error
				echo "<p style='color:red'>Error logging in, email not found </p>";
			}
		} catch (PDOException $ex) {
			echo ("Failed to connect to the database.<br>");
			echo ($ex->getMessage());
			exit;
		}
	}
}
?>

<html>

<head>
	<title>Aston Projects</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<link rel="stylesheet" href="css/styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
	<?php
	require_once ('includes/navbar.php');
	?>
	<main class="container">
		<div class="row align-items-center h-75 d-flex">
			<div class="col-lg-6">
				<h2 class="mb-4">Login</h2>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="mb-2">
						<label for="email" class="form-label">Email</label>
						<input type="text" class="form-control" name="email" required maxlength="255" />
					</div>
					<div class="col-auto mb-5">
						<label for="password" class="form-label">Password</label>
						<input type="password" class="form-control" name="password" required maxlength="20" />
					</div>
					<input class="btn btn-primary col-12" type="submit" value="Login" />
					<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>" />
				</form>
				<div class="mt-4 col-12">
					<p class="small mb-1 col-12 text-center">Not registered yet?</p>
					<a class="btn btn-outline-secondary col-12" href="register.php">Sign Up</a>
				</div>
			</div>
			<div class="col-lg-6 order-first order-lg-last text-center align-items-center d-none d-lg-block">
				<h4>Log in to add your projects to the Aston Projects database.</h4>
				<h5>Everyone is allowed to view the database.</h5>
				<h5>Users can add, edit, and delete their projects.</h5>
			</div>
		</div>
	</main>
</body>

</html>