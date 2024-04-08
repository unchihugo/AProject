<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submitted'])) {
    if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        if (!isset($_POST['title'])) {
            echo "Title is required!";
            exit();
        }

        $title = trim(htmlspecialchars($_POST['title']));
        $start_date = !empty($_POST['start_date']) ? preg_replace("([^0-9/])", "", $_POST['start_date']) : null;
        $end_date = !empty($_POST['end_date']) ? preg_replace("([^0-9/])", "", $_POST['end_date']) : null;
        $phase = !empty($_POST['phase']) ? trim(htmlspecialchars($_POST['phase'])) : null;
        $description = isset($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : false;

        if ($start_date && $end_date && strtotime($start_date) > strtotime($end_date)) {
            echo "End date must be after start date!";
        } else {

            require_once ('includes/connectdb.php');

            try {
                $stmt = $db->prepare("INSERT INTO projects (title, start_date, end_date, phase, description, uid) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $start_date, $end_date, $phase, $description, $_SESSION['uid']]);

                $project_id = $db->lastInsertId();

                header("Location: index.php");
                exit();
            } catch (PDOException $ex) {
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
    <title>Add new project</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<?php
require_once ('includes/navbar.php');
?>

<body class="min-vh-100 d-flex flex-column">
    <main class="container flex-grow-1">
        <h2 class="mb-4">Add new project</h2>
        <form method="post" action="addproject.php">
            <div class="col-lg-6 mb-3">
                <label for="title" class="form-label">Project Title</label>
                <input type="text" class="form-control" name="title" required maxlength="100" />
            </div>
            <div class="row mb-3">
                <div class="col-auto">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" />
                </div>
                <div class="col-auto">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" />
                </div>
            </div>
            <div class="col-sm-4 col-lg-2 mb-3">
                <label for="phase" class="form-label">Phase</label>
                <select name="phase" class="form-select">
                    <option value="none">-</option>
                    <option value="design">Design</option>
                    <option value="development">Development</option>
                    <option value="testing">Testing</option>
                    <option value="deployment">Deployment</option>
                    <option value="complete">Completed</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="description" class="form-label">Description</label>
                <textarea type="text" class="form-control" name="description" maxlength="500" rows="3"></textarea>
            </div>

            <input type="submit" value="Add project" class="btn btn-primary me-2" />
            <input type="reset" value="Clear all fields" class="btn btn-secondary" />
            <input type="hidden" name="submitted" value="true" />
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>" />
        </form>
    </main>
    <?php
	require_once ('includes/footer.php');
	?>
</body>

</html>