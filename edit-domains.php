<?php
session_start();
include('./timeout.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Session Timeout, Please Login Again.';
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/layouts/header.php';
if (isset($_SESSION['user_id'])) {
    $title = "Admin - Add Category";


    $domainId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql = $pdo->prepare("SELECT * FROM domains WHERE id= :domainId #is_deleted = '0'");

    $sql->bindParam(':domainId', $domainId, PDO::PARAM_INT);

    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);

?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body p-0">
                <div class="col-md-12">
                    <div class="card-header-modern d-flex align-items-center justify-content-between">
                        Edit Domain
                        <a href="javascript:history.back()" class="btn btn-danger btn-sm">
                            ← Back
                        </a>
                    </div>

                    <div class="p-2">
                        <!-- rest form / content -->
                    </div>

                    <?php if (isset($_SESSION['message'])) { ?>
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <strong>Success!</strong> <?php echo $_SESSION['message']; ?>.
                            <button type="button"
                                class="btn btn-sm btn-primary ml-3"
                                aria-label="Close"
                                onclick="closeAlert(this)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?php unset($_SESSION['message']); ?>
                        </div>
                    <?php } elseif (isset($_SESSION['error'])) { ?>
                        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <?php echo $_SESSION['error']; ?>.
                            <button type="button"
                                class="btn btn-sm btn-primary ml-3"
                                aria-label="Close"
                                onclick="closeAlert(this)">
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
                            <label for="description" class="form-label">Description (optional)</label>
                            <textarea name="domain_about" id="domain_about" class="form-control" placeholder="Describe domain here ..."><?= $data['about'] ?></textarea>
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