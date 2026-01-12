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

    $sql = $pdo->prepare("SELECT * FROM child_sub_category WHERE id= :catId AND is_deleted = '0'");
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

                    <form action="<?= $base_url ?>/src/controllers/ChildSubCategoryController.php" method="post"
                        enctype='multipart/form-data'>
                        <input type="hidden" name="uid" value="<?= $data['id'] ?>">
                        <input type="hidden" name="action" value="updateChildSubCategory">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-control" required>
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
                            <label for="subCategory" class="form-label">Sub Category Name</label>
                            <select name="subCategory" id="subCategory" class="form-select">
                                <option value="">Choose Sub Category...</option>
                                <?php foreach ($subcategories as $subcategory): ?>
                                    <option value="<?php echo htmlspecialchars($subcategory['id']); ?>" <?= $subcategory['id'] == $data['subcategory_id'] ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($subcategory['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="chsubCatName" class="form-label">Child Name</label>
                            <input type="chsubCatName" name="chsubCatName" id="chsubCatName" class="form-control"
                                value="<?= $data['child_sub_category_name'] ?>">
                        </div>

                        <div class="mb-3">
                            <label for="chhnSubCatName" class="form-label">Child Hindi Name</label>
                            <input type="chhnSubCatName" name="chhnSubCatName" id="chhnSubCatName" class="form-control"
                                value="<?= $data['hn_child_sub_category_name'] ?>">

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