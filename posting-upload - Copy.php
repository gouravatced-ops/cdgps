<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $title = "Admin - Add Category";

    require_once __DIR__ . '/src/database/Database.php';

    $database = new Database();
    $pdo = $database->getConnection();


    $sql_type = "SELECT * FROM sy_fy ORDER BY id desc";

    $types = $pdo->query($sql_type)->fetchAll(PDO::FETCH_ASSOC);

    $sql_cat = "SELECT * FROM category_master WHERE is_deleted='0'";

    $categories = $pdo->query($sql_cat)->fetchAll(PDO::FETCH_ASSOC);

    $sql_subcat = "SELECT * FROM sub_category WHERE is_deleted='0'";

    $subcategories = $pdo->query($sql_subcat)->fetchAll(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/layouts/header.php';
    ?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <h5 class="card-title fw-semibold mb-4">Post Documents</h5>

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

                    <form action="<?= $base_url ?>/src/controllers/PostingController.php" method="post"
                        enctype='multipart/form-data'>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="type" class="form-label">Type </label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">Choose Type..</option>
                                    <option value="sy">Session Year</option>
                                    <option value="fy">Financial Year</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6" id="syField">
                                <label for="sessionYearPost" class="form-label">Session Year </label>
                                <select name="sessionYearPost" id="sessionYearPost" class="form-control" required>
                                    <?php foreach ($types as $type):
                                        if ($type['calender_year'] != null) { ?>
                                            <option value="<?php echo htmlspecialchars($type['id']); ?>">
                                                <?php echo htmlspecialchars($type['calender_year']); ?>
                                            </option>
                                        <?php }endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6" id="fyField" style="display: none;">
                                <label for="financialYearPost" class="form-label">Financial Year </label>
                                <select name="financialYearPost" id="financialYearPost" class="form-control">
                                    <?php foreach ($types as $type):
                                        if ($type['financial_year'] != null) { ?>
                                            <option value="<?php echo htmlspecialchars($type['id']); ?>">
                                                <?php echo htmlspecialchars($type['financial_year']); ?>
                                            </option>
                                        <?php }endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- *********************************** -->

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="">Choose Category..</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="subCategory" class="form-label">Sub Category Name </label>
                                <select name="subCategory" id="subCategory" class="form-control">
                                    <option value="">Choose Sub Category..</option>
                                    <?php foreach ($subcategories as $subcategory): ?>
                                        <option value="<?php echo htmlspecialchars($subcategory['id']); ?>">
                                            <?php echo htmlspecialchars($subcategory['sub_category_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                            <div class="mb-3 col-md-6">
                                <label for="doc_nos" class="form-label">Document No. <span class="text-danger">*</span></label>
                                <input type="text" name="doc_nos" id="doc_nos" class="form-control" value="" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="doc_date" class="form-label">Documnet Date <span class="text-danger">*</span></label>
                                <input type="date" name="doc_date" id="doc_date" class="form-control" value="" max="<?= date("Y-m-d") ?>" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="ref_nos" class="form-label">Reference No.</label>
                                <input type="text" name="ref_nos" id="ref_nos" class="form-control" value="">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="ref_date" class="form-label">Reference Date </label>
                                <input type="date" name="ref_date" id="ref_date" class="form-control" value="" max="<?= date("Y-m-d") ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="" required>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="attachment" class="form-label">Attachment <span class="text-danger">* (Maximum
                                        2MB
                                        file.)</span></label>
                                <input type="file" name="attachment" id="attachment" class="form-control col-md-8" value="">
                                <small id="size_error" style="display:none" class="text-danger">Please check File attachment
                                    size or file
                                    type.</small>
                            </div>
                            <div class="mb-3 col-md-6">
                                <div class="col-md-6" id="preview-attachment"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="new_tag" class="form-label">New Tag <span class="text-danger">*</span></label>
                                <select name="new_tag" id="new_tag" class="form-control">
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="new_tag_day" class="form-label">Show New Tag in Days <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="new_tag_day" id="new_tag_day" class="form-control" value="">
                            </div>

                            <!-- <div class="mb-3 col-md-6">
                                <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                                <select name="status" id="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div> -->
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