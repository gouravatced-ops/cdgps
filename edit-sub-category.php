<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $title = "Admin - Add Category";

    require_once __DIR__ . '/src/database/Database.php';

    $database = new Database();
    $pdo = $database->getConnection();

    $sql = "SELECT * FROM category_master";

    $categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql = $pdo->prepare("SELECT * FROM sub_category WHERE id= :catId #is_deleted = '0'");
    $sql->bindParam(':catId', $catId, PDO::PARAM_INT);
    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);


    require_once __DIR__ . '/layouts/header.php';
    ?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <h5 class="card-title fw-semibold mb-4">Edit Sub Category</h5>

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
                            <label for="categoryId" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="categoryId" id="categoryId" class="form-control" required>
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
                            <label for="eng_cat" class="form-label">Name</label>
                            <input type="eng_cat" name="eng_cat" id="eng_cat" class="form-control"
                                value="<?= $data['sub_category_name'] ?>">
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