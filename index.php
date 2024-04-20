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

<body class="min-vh-100 d-flex flex-column">

	<?php
	session_start();
	require_once ('includes/connectdb.php');
	require_once ('includes/navbar.php');

	?>
	<main class="container flex-grow-1">
		<?php

		if (!isset($_SESSION['username'])) {
			echo "<h2> Welcome! </h2>";
			echo "<div class='alert alert-primary d-flex align-items-center my-3'>
		<svg xmlns='http://www.w3.org/2000/svg' width='16px' class='bi bi-exclamation-triangle-fill alert-primary flex-shrink-0 me-2' fill='currentColor' viewBox='0 0 16 16' role='img' aria-label='Warning:'>
    <path d='M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z'/>
  </svg><div><a class='alert-link' href='login.php'>Log in</a> to add your projects.</div></div>";
		} else {
			$username = $_SESSION['username'];
			echo "<h2> Welcome, " . $_SESSION['username'] . "!</h2>";
		}

		try {
			$query = "SELECT  * FROM  projects";
			//run the query
			$rows = $db->query($query); ?>
			<p>Total projects in database:
				<?= $rows->rowCount() ?> <br>
				<?php if (isset($_SESSION['username'])) {
					echo "Your projects are <span class='text-primary fw-bold'>highlighted</span>";
				} ?>
			</p>
			<?php if (isset($_SESSION['username'])) {
				echo "<p><a href='addproject.php' class='btn btn-primary my-3'>+ Add Project</a></p>";
			} ?>
				<div class="btn-toolbar mb-3">
					<div class="d-none d-sm-block">
						<div class="btn-group btn-group-sm me-3" role="group" aria-label="Phase Filters">
							<input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off"
								onclick="filterPhase('')" checked>
							<label class="btn btn-outline-secondary" for="btnradio1">All</label>
							<input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off"
								onclick="filterPhase('design')">
							<label class="btn btn-outline-dark" for="btnradio2">Design</label>
							<input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off"
								onclick="filterPhase('development')">
							<label class="btn btn-outline-secondary" for="btnradio3">Development</label>
							<input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off"
								onclick="filterPhase('testing')">
							<label class="btn btn-outline-primary" for="btnradio4">Testing</label>
							<input type="radio" class="btn-check" name="btnradio" id="btnradio5" autocomplete="off"
								onclick="filterPhase('deployment')">
							<label class="btn btn-outline-info" for="btnradio5">Deployment</label>
							<input type="radio" class="btn-check" name="btnradio" id="btnradio6" autocomplete="off"
								onclick="filterPhase('complete')">
							<label class="btn btn-outline-success" for="btnradio6">Complete</label>
						</div>
					</div>
					<div class="d-sm-none dropdown">
						<button class="btn btn-sm btn-secondary dropdown-toggle me-2 mb-2" type="button"
							data-bs-toggle="dropdown" aria-expanded="false">
							Phase
						</button>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="#" onclick="filterPhase('')">All</a></li>
							<li><a class="dropdown-item" href="#" onclick="filterPhase('design')">Design</a></li>
							<li><a class="dropdown-item" href="#" onclick="filterPhase('development')">Development</a></li>
							<li><a class="dropdown-item" href="#" onclick="filterPhase('testing')">Testing</a></li>
							<li><a class="dropdown-item" href="#" onclick="filterPhase('deployment')">Deployment</a></li>
							<li><a class="dropdown-item" href="#" onclick="filterPhase('complete')">Complete</a></li>
						</ul>
					</div>
					<div class="input-group input-group-sm flex-fill me-lg-3 mb-2 mb-sm-0">
						<div class="input-group-text" id="btnGroupAddon">Title</div>
						<input type="text" class="form-control" id="searchBox" onkeyup="searchTable()"
							placeholder="Search project titles" aria-describedby="btnGroupAddon">
					</div>
					<div class="input-group input-group-sm flex-fill">
						<div class="input-group-text" id="btnGroupAddon">Start date</div>
						<input type="date" class="form-control" id="dateBox" aria-describedby="btnGroupAddon">
					</div>
				</div>

				<div>
					<table class="table table-striped-columns" id="projectTable">
						<?php if (isset($_SESSION['username'])) {
							echo "<caption>Your projects are highlighted</caption>";
						} ?>
						<thead>
							<tr>
								<th class="col-8 col-md-3">Title</th>
								<th class="col-4 col-md-1 text-center">Start Date</th>
								<th class="col-md-3 col-xxl-8 d-none d-md-table-cell">Description</th>
							</tr>
						</thead>
						<tbody class="table-group-divider">
							<?php while ($row = $rows->fetch()): ?>
								<tr class="<?= $row['phase'] ?>">
									<td class="col-8 col-md-3 text-break <?php if (isset($_SESSION["uid"]) && $row['uid'] === $_SESSION["uid"]) {
										echo 'user-row';
									} ?>">
										<a class="text-decoration-none" href="project.php?id=<?= $row['pid'] ?>">
											<?= $row['title'] ?>
										</a>
										<?php if ($row['phase'] == "design"): ?>
											<span class="badge text-bg-dark">Design</span>
										<?php elseif ($row['phase'] == "development"): ?>
											<span class="badge text-bg-secondary">Development</span>
										<?php elseif ($row['phase'] == "testing"): ?>
											<span class="badge text-bg-primary">Testing</span>
										<?php elseif ($row['phase'] == "deployment"): ?>
											<span class="badge text-bg-info">Deployment</span>
										<?php elseif ($row['phase'] == "complete"): ?>
											<span class="badge text-bg-success">Complete</span>
										<?php endif; ?>
									</td>
									<td class="col-4 col-md-1 text-break text-center" id="start_date">
										<?php if(!empty($row['start_date'])) {
											echo date('d/m/Y', strtotime($row['start_date'])); 
										} ?>
									</td>
									<td class="col-md-3 col-xxl-8 text-break d-none d-md-table-cell">
										<?= $row['description'] ?>
									</td>
								</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
				<script src="js/filters.js"></script>
				<?php
		} catch (PDOexception $ex) {
			echo "Sorry, a database error occurred! <br>";
			echo "Error details: <em>" . $ex->getMessage() . "</em>";
		}

		?>
	</main>
	<?php
	require_once ('includes/footer.php');
	?>
</body>
