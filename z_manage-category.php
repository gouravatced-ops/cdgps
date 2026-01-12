<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/src/database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$sql = "SELECT * FROM category_master WHERE is_deleted='0'";

$categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Manage Category</h5>

            <table id="syFyTable" class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Name</th>
                        <th>Hindi Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i=1; foreach ($categories as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hn_category_name']); ?></td>
                            <td><a href="<?= $base_url ?>/edit-category.php?id=<?php echo htmlspecialchars($row['id']) ?>"
                                    class="btn btn-info btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;
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

<?php require_once __DIR__ . '/layouts/footer.php'; ?>