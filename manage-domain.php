<?php
session_start();
include('./timeout.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Session Timeout, Please Login Again.';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/layouts/header.php';
$module = 'domain';

$sql = "SELECT dm.* FROM domains dm  WHERE is_deleted='0' ORDER BY dm.created_date";
$domains = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern d-flex align-items-center justify-content-between">
                Manage Domains
                <?php if (canCreate($pdo, $userId, $module)) : ?>
                    <a href="<?= $base_url ?>/add-domain.php" class="btn btn-warning btn-sm">
                        <strong>+ Create</strong>
                    </a>
                <?php endif; ?>
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
            <table id="syFyTable" class="table table-bordered table-striped ">
                <thead class="thead-light">
                    <tr>
                        <th>S.No.</th>
                        <th>Name (English)</th>
                        <th>Name (Hindi)</th>
                        <th>Domain Path</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($domains as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['eng_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hin_name']); ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($row['domain_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                                    <?= htmlspecialchars($row['domain_path'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>

                            <td>
                                <?php if (canEdit($pdo, $userId, $module)) : ?>
                                    <a href="<?= $base_url ?>/edit-domains.php?id=<?php echo htmlspecialchars($row['id']) ?>"
                                        class="btn btn-primary btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;
                                <?php endif; ?>

                                <?php if (canDelete($pdo, $userId, $module)) : ?>
                                    <button class="btn btn-danger btn-sm delete-domains-button"
                                        data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<?php
$embed_script = "newsForm.js";
require_once __DIR__ . '/layouts/footer.php'; ?>