<?php
session_start();
include('./timeout.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Session Timeout, Please Login Again.';
    header('Location: index.php');
    exit;
}
if (isset($_SESSION['user_id'])) {

    require_once __DIR__ . '/layouts/header.php';
?>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body p-0">
                <div class="col-md-12">
                    <div class="card-header-modern">
                        Create Child Sub Category
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

                    <form action="<?= $base_url ?>/src/controllers/ChildSubCategoryController.php" method="post">
                        <div class="mb-3">
                            <label for="domainId" class="form-label">Domains<span class="text-danger">*</span></label>
                            <select name="domainId" id="pickDomainId" class="form-select" required>
                                <option value="">Choose Domain...</option>
                                <?php foreach ($domains_data as $values): ?>
                                    <option value="<?php echo htmlspecialchars($values['id']); ?>">
                                        <?php echo htmlspecialchars($values['eng_name']) . ' / ' . htmlspecialchars($values['hin_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="categoryId" class="form-label">Category Name<span class="text-danger">*</span></label>
                            <select name="categoryId" id="categoryId" class="form-control" required>
                                <option value="">Choose Category...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subCategoryId" class="form-label">Sub Category Name <span class="text-danger">*</span></label>
                            <select name="subCategoryId" id="subCategoryId" class="form-select" required>
                                <option value="">Choose Sub Category...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="chsubCatName" class="form-label">English Child Sub-Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="chsubCatName" name="chsubCatName" id="chsubCatName" class="form-control" value=""
                                required>

                        </div>

                        <div class="mb-3">
                            <label for="chhnSubCatName" class="form-label">Hindi Child Sub-Category Name</label>
                            <input type="chhnSubCatName" name="chhnSubCatName" id="chhnSubCatName" class="form-control"
                                value="">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (optional)</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
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