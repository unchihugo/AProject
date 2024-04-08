<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];

$project_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($project_id === null) {
    header("Location: index.php");
    exit();
}

// Connect to the database
require_once ('includes/connectdb.php');

// Fetch the project details from the database
$query = "SELECT p.*, u.username, u.email
          FROM projects p
          JOIN users u ON p.uid = u.uid
          WHERE p.pid = ?";
$stmt = $db->prepare($query);
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['delete'])) {
    if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        $query = "DELETE FROM projects WHERE pid = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$project_id]);
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>
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

<body class="min-vh-100 d-flex flex-column">
    <?php
    require_once ('includes/navbar.php');
    ?>
    <main class="container flex-grow-1">
        <?php
        if (!$project) {
            echo "<div class='alert alert-danger'>Project not found</div>";
        } else {
            ?>
            <h2 class="mb-5">Project Details</h2>
            <div class="card">
                <div class="card-body">
                    <div class="mb-2">
                        <h5 class="card-title mb-0">
                            <?= $project['title'] ?>
                            <small class="text-body-secondary ms-2">Project ID:
                                <?= $project['pid'] ?>
                            </small>
                        </h5>
                    </div>
                    <hr>
                    <div>
                        <strong>Owner:</strong>
                        <?= $project['username'] ?><br>
                        <strong>Owner Email:</strong>
                        <?= $project['email'] ?>
                    </div>
                    <hr>
                    <div class="card-text">
                        <div><strong>Start Date:</strong>
                            <?php echo ($project['start_date'] === null) ? "Not specified" : $project['start_date']; ?>
                            <strong>End Date:</strong>
                            <?php echo ($project['end_date'] === null) ? "Not specified" : $project['end_date']; ?>
                        </div>
                        <div><strong>Phase:</strong>
                            <?php echo ($project['phase'] === "") ? "None" : $project['phase']; ?>
                        </div>
                        <div><strong>Description:</strong>
                            <?php echo ($project['description'] === "") ? "No description" : $project['description']; ?>
                        </div>
                    </div>
                    <hr>
                    <a href="index.php" class="btn btn-secondary">< Return</a>
                    <?php
                    if (isset($_SESSION['uid']) && $project['uid'] == $_SESSION['uid']) {
                        ?>
                        <a href="editproject.php?id=<?= $project['pid'] ?>" class="btn btn-primary ms-1">Edit</a>
                        <button type="button" class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#delete">
                            Delete
                        </button>

                        <!-- modal -->
                        <div class="modal fade" id="delete" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Delete project</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete the project "
                                        <?php echo $project['title']; ?>"? This action cannot be undone.
                                    </div>
                                    <form class="modal-footer" method="post">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <input type="submit" name="delete" value="Confirm" class="btn btn-danger"></input>
                                        <input type="hidden" name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token'] ?? '' ?>" />
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </main>
        <?php
        require_once ('includes/footer.php');
        ?>
    </body>

    </html>
<?php } ?>