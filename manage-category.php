<?php
session_start();
include('./timeout.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Session Timeout, Please Login Again.';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/layouts/header.php';

$params = [];
$sql = "SELECT cm.*, dm.eng_name , dm.hin_name FROM category_master cm JOIN domains as dm ON dm.id = cm.domain_id WHERE cm.is_deleted='0'";

if ($domainId > 0) {
    $sql .= " AND cm.domain_id = ?";
    $params[] = $domainId;
}
$sql .= " ORDER BY cm.category_name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container-fluid">

    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern d-flex align-items-center justify-content-between">
                Manage Category

                <a href="<?= $base_url ?>/create-category.php" class="btn btn-warning btn-sm">
                    <strong>+ Create</strong>
                </a>
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
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Domain</th>
                        <th>Name (English)</th>
                        <th>Name (Hindi)</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($categories as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['eng_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hindi_category_name']); ?></td>
                            <td><a href="<?= $base_url ?>/edit-category.php?id=<?php echo htmlspecialchars($row['id']) ?>"
                                    class="btn btn-primary btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;
                                <button class="btn btn-danger btn-sm delete-category-button"
                                    data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                    <i class="ti ti-trash"></i>
                                </button>
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