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

    $sql = "SELECT * FROM category_master";

    $categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $catId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql = $pdo->prepare("SELECT * FROM child_sub_category WHERE id= :catId AND is_deleted = '0'");
    $sql->bindParam(':catId', $catId, PDO::PARAM_INT);
    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);

    $domainId = $data['domain_id'];
    $sql = "SELECT * FROM category_master WHERE domain_id = $domainId";
    $categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $categoryId = $data['category_id'];
    $sql = "SELECT * FROM sub_category WHERE category_id = $categoryId";
    $subcategories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $sql_domains = "SELECT * FROM `domains`";
    $domain_data = $pdo->query($sql_domains)->fetchAll(PDO::FETCH_ASSOC);

?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body p-0">
                <div class="col-md-12">
                    <div class="card-header-modern">
                        Edit Child Sub Category
                    </div>

                    <div class="p-2">
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

                    <form action="<?= $base_url ?>/src/controllers/ChildSubCategoryController.php" method="post"
                        enctype='multipart/form-data'>
                        <input type="hidden" name="uid" value="<?= $data['id'] ?>">
                        <input type="hidden" name="action" value="updateChildSubCategory">
                        <div class="mb-3">
                            <label for="domainId" class="form-label">Domains<span class="text-danger">*</span></label>
                            <select name="domainId" id="pickDomainId" class="form-select" required>
                                <option value="">Choose Domain...</option>
                                <?php foreach ($domain_data as $values): ?>
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
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
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
                            <label for="subCategory" class="form-label">Sub Category Name <span class="text-danger">*</span></label>
                            <select name="subCategoryId" id="subCategoryId" class="form-select" required>
                                <option value="">Choose Sub Category...</option>
                                <?php foreach ($subcategories as $subcategory): ?>
                                    <option value="<?php echo htmlspecialchars($subcategory['id']); ?>" <?= $subcategory['id'] == $data['subcategory_id'] ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($subcategory['sub_category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="chsubCatName" class="form-label">Child Name <span class="text-danger">*</span></label>
                            <input type="chsubCatName" name="chsubCatName" id="chsubCatName" class="form-control"
                                value="<?= $data['child_sub_category_name'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="chhnSubCatName" class="form-label">Child Hindi Name </label>
                            <input type="chhnSubCatName" name="chhnSubCatName" id="chhnSubCatName" class="form-control"
                                value="<?= $data['hn_child_sub_category_name'] ?>">

                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (optional)</label>
                            <textarea name="description" rows="4" id="description" class="form-control"><?= $data['description'] ?></textarea>
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