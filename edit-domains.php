<?php
session_start();
include('./timeout.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Session Timeout, Please Login Again.';
    header('Location: index.php');
    exit;
}
if (isset($_SESSION['user_id'])) {
    $title = "Admin - Add Category";

    require_once __DIR__ . '/src/database/Database.php';

    $database = new Database();
    $pdo = $database->getConnection();

    $domainId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql = $pdo->prepare("SELECT * FROM domains WHERE id= :domainId #is_deleted = '0'");

    $sql->bindParam(':domainId', $domainId, PDO::PARAM_INT);

    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/layouts/header.php';
    ?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <h5 class="card-title fw-semibold mb-4">Edit Domain</h5>

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

                    <form action="<?= $base_url ?>/src/controllers/DomainsController.php" method="post" enctype='multipart/form-data'>
                    <input type="hidden" name="uid" value="<?= $data['id'] ?>">
                    <input type="hidden" name="action" value="updateDomains">
                        <div class="mb-3">
                            <label for="eng_name" class="form-label">Name</label>
                            <input type="text" name="eng_name" id="eng_name" class="form-control" value="<?= $data['eng_name'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="hin_name" class="form-label">Name</label>
                            <input type="text" name="hin_name" id="hin_name" class="form-control" value="<?= $data['hin_name'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="domain_path" class="form-label">Domain Path<span class="text-danger">*</span></label>
                            <input type="text" name="domain_path" id="domain_path" placeholder="domainName.example.com" class="form-control" value="<?= $data['domain_path'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="domain_about" class="form-label">Description</label>
                            <input type="text" name="domain_about" id="domain_about" placeholder="Domain is ..." class="form-control" value="<?= $data['about'] ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    $embed_script = "restriction.js";
    require_once __DIR__ . '/layouts/footer.php'; ?>
<?php } else {
    echo "Invalid session, <a href='index.php'>click here</a> to login";
}
?>