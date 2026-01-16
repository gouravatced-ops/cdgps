<?php
session_start();
require_once __DIR__ . '/layouts/header.php';

$limit = 20;

// current page  id=34&&page=2
$limit = 20;

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
    'options' => ['default' => 1, 'min_range' => 1]
]);

$loginId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$loginId) {
    die('Invalid login session');
}

$offset = ($page - 1) * $limit;

$countSql = "
    SELECT COUNT(*)
    FROM activity_logs al
    WHERE al.login_id = :login_id
";

$stmt = $pdo->prepare($countSql);
$stmt->bindValue(':login_id', $loginId, PDO::PARAM_INT);
$stmt->execute();

$totalRecords = (int)$stmt->fetchColumn();
$totalPages   = (int)ceil($totalRecords / $limit);


$sql = "
    SELECT
        al.id AS activity_id,
        al.table_name,
        al.record_id,
        al.action AS activity_action,
        al.column_name,
        al.old_value,
        al.new_value,
        al.performed_by,
        al.performed_at,
        al.ip_address AS activity_ip,

        u.username,
        ul.action AS login_action,
        ul.created_at AS login_time,
        ul.ip AS login_ip

    FROM activity_logs al
    INNER JOIN users_logs ul ON ul.id = al.login_id
    INNER JOIN users u ON u.id = ul.user_id
    WHERE al.login_id = :login_id
    ORDER BY al.performed_at DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':login_id', $loginId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$activityLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// echo "<pre>";
// print_r($activityLogs);
// die();
// echo "</pre";

$maxVisible = 5;

$startPage = max(1, $page - floor($maxVisible / 2));
$endPage   = $startPage + $maxVisible - 1;

if ($endPage > $totalPages) {
    $endPage = $totalPages;
    $startPage = max(1, $endPage - $maxVisible + 1);
}

function prettyJson($json)
{
    if (empty($json)) return '';

    $decoded = json_decode($json, true);
    if (!$decoded) return '';

    return json_encode(
        $decoded,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
}


?>
<style>
    .accordion-content {
        display: none;
        background: #f9fafb;
    }

    pre {
        background: #111827;
        color: #e5e7eb;
        padding: 10px;
        border-radius: 6px;
        font-size: 12px;
        overflow-x: auto;
    }

    .badge-active {
        color: #065f46;
        font-weight: 600;
    }

    .badge-inactive {
        color: #991b1b;
        font-weight: 600;
    }
</style>
<div class="container-fluid">

    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern d-flex align-items-center justify-content-between">
                <span>Activity Logs</span>
                <a href="javascript:history.back()" class="btn btn-danger btn-sm">
                    ← Back
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
            <table class="table table-bordered table-striped">
                <tbody>
                    <?php
                    $i = $offset + 1;

                    foreach ($activityLogs as $row):
                        $isDelete = ($row['activity_action'] === 'DELETE');
                    ?>
                        <!-- Main Row -->
                        <tr class="activity-row">
                            <td><?= $i++; ?>
                                <hr>
                                <strong>Module:</strong> <?= htmlspecialchars(strtoupper($row['table_name'])) ?><br>
                                <strong>Action: </strong> <?php if ($row['activity_action'] === 'INSERT'): ?>
                                    <span class="badge me-3 d-none d-md-inline-block role-badge">INSERT</span>
                                <?php elseif ($row['activity_action'] === 'UPDATE'): ?>
                                    <span class="badge me-3 d-none d-md-inline-block" style="background: linear-gradient(90deg,#00ccff 60%,#0492e4 100%);">UPDATE</span>
                                <?php elseif ($row['activity_action'] === 'DELETE'): ?>
                                    <span class="badge me-3 d-none d-md-inline-block" style="background: linear-gradient(90deg,#d30606 60%,#920404 100%);">DELETE</span>
                                <?php endif; ?><br>
                                <strong>Date:</strong> <?= htmlspecialchars($row['performed_at']) ?><br>

                                <?php if ($row['activity_action'] === 'DELETE'): ?>
                                    <strong>Status:</strong>
                                    <?= ($row['new_value'] == 0)
                                        ? '<span class="badge-active">Active</span>'
                                        : '<span class="badge-inactive">Inactive</span>' ?>
                                    <h6>Old Value</h6>
                                    <pre><?= $row['old_value'] ?></pre>
                                <?php endif; ?>

                                <?php if (!empty($row['old_value'])): ?>
                                    <h6>Old Value</h6>
                                    <pre><?= prettyJson($row['old_value']) ?></pre>
                                <?php endif; ?>

                                <?php if (!empty($row['new_value'])): ?>
                                    <h6>New Value</h6>
                                    <pre><?= prettyJson($row['new_value']) ?></pre>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($activityLogs)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No activity found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-end">

                        <!-- Previous -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>

                        <!-- First Page -->
                        <?php if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Last Page -->
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $totalPages ?>">
                                    <?= $totalPages ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Next -->
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>

                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$embed_script = "newsForm.js";
require_once __DIR__ . '/layouts/footer.php'; ?>