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
    require_once __DIR__ . '/layouts/header.php';
?>

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body p-0">

                <!-- Header -->
                <div class="card-header-modern d-flex align-items-center justify-content-between">
                    Add User
                </div>

                <!-- Alerts -->
                <div class="px-3 pt-3">
                    <?php if (!empty($_SESSION['message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $_SESSION['message'];
                            unset($_SESSION['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['error'];
                            unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Form -->
                <form action="<?= $base_url ?>/src/controllers/UserController.php" method="post">
                    <input type="hidden" name="action" value="actionCreateUser">
                    <?php
                    if (isset($_SESSION['error_message'])) {
                        echo '<div style="color: red;">' . $_SESSION['error_message'] . '</div><br>';
                        unset($_SESSION['error_message']);
                    }
                    $errors = isset($_SESSION['req_error_msg']) ? $_SESSION['req_error_msg'] : '';
                    ?>
                    <div class="p-2">
                        <div class="row g-3">
                            <!-- Domain -->
                            <div class="col-md-6">
                                <label class="form-label">Domain</label>
                                <select name="domain_id" class="form-select" <?= ($domainId > 0) ? 'disabled' : '' ?>>
                                    <option value="">Choose Domain...</option>
                                    <?php foreach ($domains_data as $domain): ?>
                                        <option value="<?= $domain['id'] ?>">
                                            <?= $domain['eng_name'] . ' - ' . $domain['hin_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <?php if ($domainId > 0): ?>
                                    <input type="hidden" name="domain_id" value="<?= (int)$domainId; ?>">
                                <?php endif; ?>

                                <?php if (isset($errors['domain_id'])): ?>
                                    <div class="invalid-feedback"><?= $errors['domain_id'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <label class="form-label">Username<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label">Email ID <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label class="form-label">Phone No <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" min="10" max="10" required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <label class="form-label">Role Type <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="admin">Admin</option>
                                    <option value="coadmin">Co-Admin</option>
                                </select>
                                <?php if (isset($errors['role'])): ?>
                                    <div class="invalid-feedback"><?= $errors['role'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password" class="form-control" required>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Password Expiry -->
                            <div class="col-md-6">
                                <label class="form-label">Password Expiry</label>
                                <select name="password_expire_in_days" class="form-select">
                                    <option value="90" selected>90 Days (Default)</option>
                                    <option value="60">60 Days</option>
                                    <option value="30">30 Days</option>
                                    <option value="15">15 Days</option>
                                </select>
                            </div>

                            <!-- Permissions -->
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-2">Permissions Modules</label>
                                <div class="row g-2">
                                    <?php if (isset($errors['permission'])): ?>
                                        <div class="text-danger small mb-2">
                                            <?= $errors['permission'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    $permissions = [
                                        'category' => 'Category',
                                        'subcategory' => 'Sub Category',
                                        'childsubcategory' => 'Child Sub Category',
                                        'news' => 'News',
                                        'notices' => 'Notices',
                                        'media' => 'Gallery',
                                        'tenders' => 'Tenders',
                                        'users' => 'Users',
                                        'permission' => 'Permissions'
                                    ];

                                    foreach ($permissions as $key => $label):
                                    ?>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="permission[]"
                                                    value="<?= $key ?>"
                                                    id="perm_<?= $key ?>">
                                                <label class="form-check-label" for="perm_<?= $key ?>">
                                                    <?= $label ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    Create User
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

<?php
    $embed_script = "restriction.js";
    require_once __DIR__ . '/layouts/footer.php';
} else {
    echo "Invalid session, <a href='index.php'>click here</a> to login";
}
?>