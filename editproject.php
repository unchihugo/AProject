<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];

$project_id = isset($_GET['id']) ? $_GET['id'] : null;

require_once ('includes/connectdb.php');

$query = "SELECT p.*, u.username, u.email
          FROM projects p
          JOIN users u ON p.uid = u.uid
          WHERE p.pid = ?";

$stmt = $db->prepare($query);
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// if ($project['uid'] !== $_SESSION['uid']) {
//     header("Location: index.php");
//     exit();
// }

if (isset($_POST['submitted'])) {
    if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        if (!isset($_POST['title'])) {
            echo "Title is required!";
            exit();
        }

        $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : null;
        $title = trim(htmlspecialchars($_POST['title']));
        $start_date = !empty($_POST['start_date']) ? preg_replace("([^0-9/])", "", $_POST['start_date']) : null;
        $end_date = !empty($_POST['end_date']) ? preg_replace("([^0-9/])", "", $_POST['end_date']) : null;
        $phase = !empty($_POST['phase']) ? trim(htmlspecialchars($_POST['phase'])) : null;
        $description = isset($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : false;

        if ($start_date && $end_date && strtotime($start_date) > strtotime($end_date)) {
            echo "End date must be after start date!";
        } else {

            try {
                $stmt = $db->prepare("UPDATE projects SET title = ?, start_date = ?, end_date = ?, phase = ?, description = ? WHERE pid = ?");
                $stmt->execute([$title, $start_date, $end_date, $phase, $description, $project_id]);

                header("Location: project.php?id={$project_id}");
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
    <title>Editing
        <?php echo $project['title'] ?>
    </title>
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
        <?php
        if (!$project || $project['uid'] !== $_SESSION['uid']) {
            echo "<div class='alert alert-danger'>Cannot edit project</div>";
        } else { ?>
            <h2 class="mb-4">Edit project:
                <?php echo $project['title'] ?>
            </h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="col-lg-6 mb-3">
                    <label for="title" class="form-label">Project Title</label>
                    <input type="text" class="form-control" name="title" required maxlength="100"
                        value="<?php echo $project['title'] ?>" />
                </div>
                <div class="row mb-3">
                    <div class="col-auto">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date"
                            value="<?php echo $project['start_date'] ?>" />
                    </div>
                    <div class="col-auto">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date"
                            value="<?php echo $project['end_date'] ?>" />
                    </div>
                </div>
                <div class="col-sm-4 col-lg-2 mb-3">
                    <label for="phase" class="form-label">Phase</label>
                    <select name="phase" class="form-select">
                        <option value="none" <?php echo ($project['phase'] === 'none') ? 'selected' : ''; ?>>-</option>
                        <option value="design" <?php echo ($project['phase'] === 'design') ? 'selected' : ''; ?>>Design
                        </option>
                        <option value="development" <?php echo ($project['phase'] === 'development') ? 'selected' : ''; ?>>
                            Development</option>
                        <option value="testing" <?php echo ($project['phase'] === 'testing') ? 'selected' : ''; ?>>Testing
                        </option>
                        <option value="deployment" <?php echo ($project['phase'] === 'deployment') ? 'selected' : ''; ?>>
                            Deployment</option>
                        <option value="complete" <?php echo ($project['phase'] === 'complete') ? 'selected' : ''; ?>>Completed
                        </option>
                    </select>

                </div>
                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea type="text" class="form-control" name="description" maxlength="500"
                        rows="3"><?php echo $project['description'] ?></textarea>
                </div>

                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                <input type="hidden" name="submitted" value="true" />
                <input type="submit" value="Save Edits" class="btn btn-primary me-2" />
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>" />
            </form>
        <?php } ?>
    </main>
    <?php
    require_once ('includes/footer.php');
    ?>
</body>

</html>