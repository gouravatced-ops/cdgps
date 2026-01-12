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

    $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql = $pdo->prepare("SELECT * FROM category_master WHERE id= :catId #is_deleted = '0'");

    $sql->bindParam(':catId', $catId, PDO::PARAM_INT);

    // $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);


    $sql_commissionerates = "SELECT * FROM `domains`";
    $commr_data = $pdo->query($sql_commissionerates)->fetchAll(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/layouts/header.php';
?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <h5 class="card-title fw-semibold mb-4">Edit Category</h5>

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

                    <form action="<?= $base_url ?>/src/controllers/CategoryController.php" method="post" enctype='multipart/form-data'>
                        <input type="hidden" name="uid" value="<?= $data['id'] ?>">
                        <input type="hidden" name="action" value="updateCategory">
                        <div class="mb-3">
                            <label for="domainId" class="form-label">Domains<span class="text-danger">*</span></label>
                            <select name="domainId" id="domainId" class="form-select" required>
                                <option value="">Choose Domain...</option>
                                <?php foreach ($commr_data as $values): ?>
                                    <?php $selected = (isset($data['domain_id']) && $data['domain_id'] == $values['id']) ? "selected" : ""; ?>
                                    <option value="<?php echo htmlspecialchars($values['id']); ?> " <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($values['eng_name']) . ' / ' . htmlspecialchars($values['hin_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="eng_cat" class="form-label">Name (English)</label>
                            <input type="text" name="eng_cat" id="eng_cat" class="form-control" value="<?= $data['category_name'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="hin_cat" class="form-label">Name (Hindi)</label>
                            <input type="text" name="hin_cat" id="hin_cat" class="form-control" value="<?= $data['hindi_category_name'] ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/layouts/footer.php'; ?>
<?php } else {
    echo "Invalid session, <a href='index.php'>click here</a> to login";
}
?>