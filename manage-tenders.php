<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/layouts/header.php';

$sql = "SELECT *, b.sub_category_name as category_name FROM tenders a join sub_category b on a.tender_category = b.id WHERE  a.is_deleted='0' ORDER BY created_at";

$categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <div class="card">
                    <div class="card-body p-0">
                <div class="card-header-modern">
                    Manage Tenders
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
                        <th>Financial Year</th>
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
                            <td><?php echo htmlspecialchars($row['financial_year']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['tender_title']); ?></td>
                            <td><?= htmlspecialchars($row['tender_dated']); ?></td>
                            <td><a href="<?= $base_url ?>/edit-tenders.php?id=<?php echo htmlspecialchars($row['uniq_id']) ?>"
                                    class="btn btn-info btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;

                                <form action="<?= $base_url ?>/src/controllers/tender/EditTenderController.php"
                                    method="post">
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