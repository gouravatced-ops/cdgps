<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/src/database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$sql = "SELECT a.*, b.category_name , dm.eng_name FROM sub_category a INNER JOIN category_master b ON b.id = a.category_id LEFT JOIN domains as dm ON dm.id = a.domain_id WHERE a.is_deleted='0'";

$postings = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern">
                Manage Sub Category
            </div>

            <div class="p-3">
                <!-- rest form / content -->
            </div>

            <table id="syFyTable" class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Domain</th>
                        <th>Category Name</th>
                        <th>Name (English)</th>
                        <th>Name (Hindi)</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($postings as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['eng_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['sub_category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hindi_sub_category_name']); ?></td>

                            <td><a href="<?= $base_url ?>/edit-sub-category.php?id=<?= htmlspecialchars($row['id']) ?>"
                                    class="btn btn-info btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;
                                <button class="btn btn-danger btn-sm delete-sub-category-button"
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