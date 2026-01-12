<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/src/database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$sql = "SELECT *, b.sub_category_name as category_name FROM notices a join sub_category b on a.notice_category = b.id WHERE  a.is_deleted='0' ORDER BY created_at";

$categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Manage Notices</h5>
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
                        <th>Tender Category</th>
                        <th>Tender Title</th>
                        <th>Dated</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($categories as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['notice_ref_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['notice_title']); ?></td>
                            <td><?= htmlspecialchars($row['notice_dated']); ?></td>
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