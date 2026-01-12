<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $title = "Admin - Add Category";

    require_once __DIR__ . '/src/database/Database.php';

    $database = new Database();
    $pdo = $database->getConnection();

    $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // $sql = $pdo->prepare("SELECT * FROM type ");
    $sql_cat = "SELECT * FROM `type`";
    $data = $pdo->query($sql_cat)->fetchAll(PDO::FETCH_ASSOC);

    // -- $data = $sql->fetch(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/layouts/header.php';
    ?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <h5 class="card-title fw-semibold mb-4">Create Category</h5>

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

                    <form action="<?= $base_url ?>/src/controllers/CategoryController.php" method="post"
                        enctype='multipart/form-data'>
                        <div class="mb-3">
                            <label for="eng_cat" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="eng_cat" name="eng_cat" id="eng_cat" class="form-control" value="" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/layouts/footer.php'; } else {
    echo "Invalid session, <a href='index.php'>click here</a> to login";
}
?>