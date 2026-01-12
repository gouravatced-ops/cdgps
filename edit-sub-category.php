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

    $sql_commissionerates = "SELECT * FROM `domains`";
    $commr_data = $pdo->query($sql_commissionerates)->fetchAll(PDO::FETCH_ASSOC);

    $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql = $pdo->prepare("SELECT * FROM sub_category WHERE id= :catId #is_deleted = '0'");
    $sql->bindParam(':catId', $catId, PDO::PARAM_INT);
    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);

    $domainId = $data['domain_id'];

    $sql = "SELECT * FROM category_master WHERE domain_id = $domainId";
    $categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/layouts/header.php';
?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body p-0">
                <div class="col-md-12">
                    <div class="card-header-modern">
                        Edit Sub Category
                    </div>

                    <div class="p-3">
                        <!-- rest form / content -->
                    </div>

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

                    <form action="<?= $base_url ?>/src/controllers/SubCategoryController.php" method="post"
                        enctype='multipart/form-data'>
                        <input type="hidden" name="uid" value="<?= $data['id'] ?>">
                        <input type="hidden" name="action" value="updateSubCategory">
                        <div class="mb-3">
                            <label for="domainId" class="form-label">Domains<span class="text-danger">*</span></label>
                            <select name="domainId" id="pickDomainId" class="form-select" required>
                                <option value="">Choose Domain...</option>
                                <?php foreach ($commr_data as $values): ?>
                                    <option value="<?php echo htmlspecialchars($values['id']); ?>"
                                        <?php if (!empty($domainId) && $domainId == $values['id']) echo 'selected'; ?>>
                                        <?php
                                        echo htmlspecialchars($values['eng_name']) . ' / ' .
                                            htmlspecialchars($values['hin_name']);
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="categoryId" class="form-label">Category Name<span class="text-danger">*</span></label>
                            <select name="categoryId" id="categoryId" class="form-control">
                                <option value="">Choose Category...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['id']); ?>"
                                        <?= $category['id'] == $data['category_id'] ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="eng_sub_cat" class="form-label">Name (English) <span class="text-danger">*</span></label>
                            <input type="eng_sub_cat" name="eng_sub_cat" id="eng_sub_cat" class="form-control"
                                value="<?= $data['sub_category_name'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="hin_sub_cat" class="form-label">Name (Hindi)</label>
                            <input type="hin_sub_cat" name="hin_sub_cat" id="hin_sub_cat" class="form-control"
                                value="<?= $data['hindi_sub_category_name'] ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    $embed_script = "newsForm.js";
    require_once __DIR__ . '/layouts/footer.php'; ?>
<?php } else {
    echo "Invalid session, <a href='index.php'>click here</a> to login";
}
?>