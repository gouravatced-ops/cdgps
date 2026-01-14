<?php

/**
 * Manage Profile Page
 * Protected page with session check
 */
require_once __DIR__ . '/src/helpers/session_helper.php';
requireLogin();

require_once __DIR__ . '/layouts/header.php';
// Get current user data
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, email, username FROM users WHERE id = :userId");
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header("Location: dashboard_view.php");
    exit;
}

?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern">
                Manage Profile
            </div>

            <div class="p-2">
                <!-- rest form / content -->
            </div>

            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <strong>Success!</strong> <?php echo $_SESSION['message']; ?>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php } elseif (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <?php echo $_SESSION['error']; ?>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <form action="<?= $base_url ?>/src/controllers/ProfileController.php" method="post" id="profileForm">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">

                <div class="mb-4">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" class="form-control" name="username" id="username"
                        value="<?= htmlspecialchars($user['username'] ?? ''); ?>"
                        placeholder="Enter your username" required />
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" id="email"
                        value="<?= htmlspecialchars($user['email'] ?? ''); ?>"
                        placeholder="Enter your email" required />
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>