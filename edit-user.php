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

    $userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql = $pdo->prepare("SELECT * FROM users WHERE id= :userId #is_deleted = '0'");

    $sql->bindParam(':userId', $userId, PDO::PARAM_INT);

    $sql->execute();
    $result = $sql->fetch(PDO::FETCH_ASSOC);

    $data = $result ?? [];
    $errors = $errors ?? [];

    $selectedPermissions = [];
    if (!empty($data['permission'])) {
        $selectedPermissions = json_decode($data['permission'], true) ?? [];
    }

    $isEdit = !empty($data['id']);

?>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="card-header-modern d-flex align-items-center justify-content-between">
                    Edit User
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm">
                        ← Back
                    </a>
                </div>

                <div class="p-2">
                    <!-- rest form / content -->
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
                    <input type="hidden" name="action" value="actionUpdateUser">
                    <input type="hidden" name="userId" value="<?= $userId ?>">
                    <div class="p-2">
                        <div class="row g-3">

                            <!-- Domain -->
                            <div class="col-md-6">
                                <label class="form-label">Domain</label>
                                <select name="domain_id"
                                    class="form-select <?= isset($errors['domain_id']) ? 'is-invalid' : '' ?>"
                                    <?= ($domainId > 0) ? 'disabled' : '' ?>>

                                    <option value="">Choose Domain...</option>
                                    <?php foreach ($domains_data as $domain): ?>
                                        <option value="<?= (int)$domain['id']; ?>"
                                            <?= (!empty($data['domain_id']) && $data['domain_id'] == $domain['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($domain['eng_name'] . ' / ' . $domain['hin_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <?php if ($domainId > 0): ?>
                                    <input type="hidden" name="domain_id" value="<?= (int)$domainId; ?>">
                                <?php endif; ?>

                                <div class="invalid-feedback"><?= $errors['domain_id'] ?? '' ?></div>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="username"
                                    class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                    value="<?= htmlspecialchars($data['username'] ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"><?= $errors['username'] ?? '' ?></div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label">Email ID <span class="text-danger">*</span></label>
                                <input type="email"
                                    name="email"
                                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                            </div>

                            <!-- Mobile -->
                            <div class="col-md-6">
                                <label class="form-label">Mobile No <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="mobile"
                                    class="form-control <?= isset($errors['mobile']) ? 'is-invalid' : '' ?>"
                                    maxlength="10"
                                    value="<?= htmlspecialchars($data['mobile'] ?? '') ?>"
                                    required>
                                <div class="invalid-feedback"><?= $errors['mobile'] ?? '' ?></div>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <label class="form-label">Role Type <span class="text-danger">*</span></label>
                                <select name="role"
                                    class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>"
                                    required>
                                    <option value="">Select Role</option>
                                    <option value="admin" <?= ($data['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="coadmin" <?= ($data['role'] ?? '') === 'coadmin' ? 'selected' : '' ?>>Co-Admin</option>
                                </select>
                                <div class="invalid-feedback"><?= $errors['role'] ?? '' ?></div>
                            </div>

                            <!-- Password (Only required on Create) -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Password <?= $isEdit ? '(Leave blank to keep current)' : '<span class="text-danger">*</span>' ?>
                                </label>
                                <input type="password"
                                    name="updatepassword"
                                    class="form-control <?= isset($errors['updatepassword']) ? 'is-invalid' : '' ?>"
                                    <?= $isEdit ? '' : 'required' ?>>
                                <div class="invalid-feedback"><?= $errors['updatepassword'] ?? '' ?></div>
                            </div>

                            <!-- Password Expiry -->
                            <div class="col-md-6">
                                <label class="form-label">Password Expiry (Days)</label>
                                <select name="password_expire_in_days" class="form-select">
                                    <?php foreach ([90, 60, 30, 15] as $days): ?>
                                        <option value="<?= $days ?>"
                                            <?= ($data['password_expire_in_days'] ?? 90) == $days ? 'selected' : '' ?>>
                                            <?= $days ?> Days
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Permissions -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Permissions Modules</label>
                                <div class="row g-2">
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
                                                    id="perm_<?= $key ?>"
                                                    <?= in_array($key, $selectedPermissions) ? 'checked' : '' ?>>
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
                                    Update User
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