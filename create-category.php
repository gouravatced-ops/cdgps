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
                        Create Category
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

                    <form action="<?= $base_url ?>/src/controllers/CategoryController.php" method="post"
                        enctype='multipart/form-data'>
                        <div class="mb-3">
                            <label for="domainId" class="form-label">Domains<span class="text-danger">*</span></label>
                            <select name="domainId" id="domainId" class="form-select"
                                <?= ($domainId > 0) ? 'disabled' : '' ?>>
                                <option value="">Choose Domain...</option>
                                <?php foreach ($domains_data as $values): ?>
                                    <option value="<?= (int)$values['id']; ?>"
                                        <?= (!empty($domainId) && $domainId == $values['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($values['eng_name']) ?>
                                        <?= !empty($values['hin_name']) ? ' / ' . htmlspecialchars($values['hin_name']) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if ($domainId > 0): ?>
                                <input type="hidden" name="domainId" value="<?= (int)$domainId; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="eng_cat" class="form-label">Name (English)<span class="text-danger">*</span></label>
                            <input type="eng_cat" name="eng_cat" id="eng_cat" class="form-control" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="hin_cat" class="form-label">Name (Hindi)</label>
                            <input type="hin_cat" name="hin_cat" id="hin_cat" class="form-control" value="">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/layouts/footer.php';
} else {
    echo "Invalid session, <a href='index.php'>click here</a> to login";
}
?>