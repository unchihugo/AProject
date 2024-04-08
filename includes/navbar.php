<nav class="navbar navbar-expand-lg border-bottom mb-3 bg-light sticky-top">
    <div class="container">
        <a class="navbar-brand text-primary-emphasis" href="index.php"><span class="fw-bold">Aston</span> Projects</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-center" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center <?php if (!isset($_SESSION['username'])) echo 'disabled' ?>" href="addproject.php">Add Project
                    <span class="badge text-bg-secondary <?php if (!isset($_SESSION['username'])) echo 'opacity-75' ?>">Users only</span></a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item me-lg-2">
                        <span class="nav-link text-center text-primary-emphasis">
                            <?= $_SESSION['username'] ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-secondary col-12 col-lg-auto" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-lg-2">
                        <a class="btn btn-outline-primary col-12 col-lg-auto" href="register.php">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary col-12 col-lg-auto" href="login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>