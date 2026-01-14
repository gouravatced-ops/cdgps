<?php

/**
 * Manage Notices Page
 * Protected page with session check
 */
require_once __DIR__ . '/src/helpers/session_helper.php';
requireLogin(); // This will redirect if not logged in or session expired

require_once __DIR__ . '/layouts/header.php'; 

$params = [];
$sql = "SELECT *, b.sub_category_name as category_name , dm.eng_name , csc.child_sub_category_name FROM notices a join sub_category b on a.notice_subcategory = b.id LEFT JOIN domains as dm ON dm.id = a.domain_id LEFT JOIN child_sub_category as csc ON csc.id = a.notice_childsubcategory WHERE  a.is_deleted='0' ";

if ($domainId > 0) {
    $sql .= " AND a.domain_id = ?";
    $params[] = $domainId;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$notices_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">

    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern">
                Manage Notices
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
                        <th>Ref. No.</th>
                        <th>Domain</th>
                        <th>Category</th>
                        <th style="white-space: nowrap;">Sub Category</th>
                        <th style="white-space: nowrap;">Notice Title</th>
                        <th>Dated</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($notices_data as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['notice_ref_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['eng_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['child_sub_category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['notice_title']); ?></td>
                            <td style="white-space: nowrap;"><?= htmlspecialchars($row['notice_dated']); ?></td>
                            <td><a href="<?= $base_url ?>/edit-notices.php?id=<?php echo htmlspecialchars($row['uniq_id']) ?>"
                                    class="btn btn-info btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;

                                <form action="<?= $base_url ?>/src/controllers/notice/NoticeController.php" method="post">
                                    <input type="hidden" name="ed" value="<?php echo htmlspecialchars($row['uniq_id']); ?>">
                                    <button class="btn btn-danger btn-sm delete-category-button" type="submit">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>