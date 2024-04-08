<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
		exit;
	} else {
		if (!isset($_POST['username'], $_POST['password'], $_POST['repeatPassword'], $_POST['email'], $_POST['repeatEmail']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['repeatPassword']) || empty($_POST['repeatEmail'])) {
			// Could not get the data that should have been sent, or fields are empty
			exit('Please fill all the fields!');
		}

		require_once ('includes/connectdb.php');

		$username = trim(htmlspecialchars($_POST['username']));
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		$repeatEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo "<div class='alert alert-danger d-flex align-items-center my-3 container'>
			<svg xmlns='http://www.w3.org/2000/svg' width='16px' class='bi bi-exclamation-triangle-fill alert-primary flex-shrink-0 me-2' fill='currentColor' viewBox='0 0 16 16' role='img' aria-label='Warning:'>
		<path d='M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z'/>
	  </svg><div>Email already exists!</div></div>";
		} elseif (htmlspecialchars($_POST['password']) !== htmlspecialchars($_POST['repeatPassword'])) {
			echo "<div class='alert alert-danger d-flex align-items-center my-3 container'>
			<svg xmlns='http://www.w3.org/2000/svg' width='16px' class='bi bi-exclamation-triangle-fill alert-primary flex-shrink-0 me-2' fill='currentColor' viewBox='0 0 16 16' role='img' aria-label='Warning:'>
		<path d='M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z'/>
	  </svg><div>Passwords do not match!</div></div>";
		} elseif ($email !== $repeatEmail) {
			echo "<div class='alert alert-danger d-flex align-items-center my-3 container'>
			<svg xmlns='http://www.w3.org/2000/svg' width='16px' class='bi bi-exclamation-triangle-fill alert-primary flex-shrink-0 me-2' fill='currentColor' viewBox='0 0 16 16' role='img' aria-label='Warning:'>
		<path d='M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z'/>
	  </svg><div>Emails do not match!</div></div>";
		} else {
			// check if email already exists
			$checkEmail = $db->prepare("SELECT * FROM users WHERE email = ?");
			$checkEmail->execute(array($email));
			if ($checkEmail->rowCount() > 0) {
				echo ('Email already exists!');
			}

			try {

				$stat = $db->prepare("insert into users values(default,?,?,?)");
				$stat->execute(array($username, $password, $email));

				$id = $db->lastInsertId();
				session_start();
				$_SESSION["username"] = $username;
				$_SESSION["uid"] = $id;
				header("Location:index.php");
				exit();

			} catch (PDOexception $ex) {
				echo "Sorry, a database error occurred! <br>";
				echo "Error details: <em>" . $ex->getMessage() . "</em>";
			}
		}
	}
}
?>
<!DOCTYPE html>
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
	<h2 class="mb-4">Register</h2>
		<div class="row align-items-center h-75 d-flex">
			<div class="col-lg-6">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="mb-4">
						<label for="username" class="form-label">Username</label>
						<input type="text" class="form-control" name="username" required maxlength="255" />
					</div>
					<div class="mb-2">
						<label for="email" class="form-label">Email</label>
						<input type="email" class="form-control" name="email" required maxlength="255"
							placeholder="name@email.com" />
					</div>
					<div class="mb-4">
						<label for="repeatEmail" class="form-label">Repeat Email</label>
						<input type="email" class="form-control" name="repeatEmail" required maxlength="255"
							placeholder="name@email.com" />
					</div>
					<div class="col-auto"><label for="password" class="form-label">Password</label></div>
					<div class="mb-1 row g-3 align-items-center ">
						<div class="col-auto">
							<input type="password" class="form-control" name="password" required maxlength="20"
								placeholder="********************" />
						</div>
						<div class="col-auto">
							<span id="passwordHelpInline" class="form-text">
								Must be 8-20 characters long, and must not contain special characters.
							</span>
						</div>
					</div>
					<div class="col-auto"><label for="repeatPassword" class="form-label">Repeat Password</label></div>
					<div class="mb-5 row g-3 align-items-center">
						<div class="col-auto">
							<input type="password" class="form-control" name="repeatPassword" required maxlength="20"
								placeholder="********************" />
						</div>
					</div>
					<input class="btn btn-primary col-12" type="submit" value="Register" />
					<input type="hidden" name="submitted" value="true" />
					<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>" />
				</form>
				<div class="mt-4 col-12">
					<p class="small mb-1 col-12 text-center">Already a user?</p>
					<a class="btn btn-outline-secondary col-12" href="login.php">Log in</a>
				</div>
			</div>
			<div class="col-lg-6 order-first order-lg-last text-center align-items-center d-none d-lg-block">
				<h4>Register to add your projects to the Aston Projects database.</h4>
				<h5>Everyone is allowed to view the database.</h5>
				<h5>Users can add, edit, and delete their projects.</h5>
			</div>
		</div>
	</main>
</body>

</html>