<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update Password</h5>

            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <strong>Success!</strong> <?php echo $_SESSION['message']; ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php } elseif (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <?php echo $_SESSION['error']; ?>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <form action="<?= $base_url ?>/src/controllers/UpdatePasswordController.php" method="post" id="updatePass" >

                <div class="mb-4">
                    <label for="UserPassword" class="form-label">Password:</label>
                    <input type="password" class="form-control" name="password" id="UserPassword" 
                        placeholder="Enter your password" />
                    <small id="passwordHelp" class="form-text text-info">
                        Note : Password should be 8 char(min) and contain one special character, number, Upper Case and Lower Case.
                    </small>
                    <div id="passwordError" class="text-danger mt-2" style="display: none;">Invalid password format.</div>
                </div>
                <div class="mb-4">
                    <label for="confirmPassword" class="form-label">Confirm Password:</label>
                    <input type="text" class="form-control" name="confirmPassword" id="confirmPassword" 
                        placeholder="Enter your confirm password" />
                    <div id="passwordMismatch" class="text-danger mt-2" style="display: none;">Passwords do not match.</div>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/layouts/footer.php'; ?>
