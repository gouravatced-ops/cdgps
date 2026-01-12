<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/src/database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$sql = "SELECT p.*, cm.category_name as cat_name, sc.sub_category_name  FROM postings p INNER JOIN category_master cm ON p.category = cm.id LEFT JOIN  sub_category sc on p.sub_category = sc.id where p.is_deleted='0' order by p.created_on asc";

$postings = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Manage Postings</h5>

            <table id="postingTable" class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Category</th>
                        <th>Sub-Category</th>
                        <th>Dated</th>
                        <th>Document No.</th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($postings as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['cat_name']); ?></td>
                            <td><?php echo empty(htmlspecialchars($row['sub_category'])) ? 'NA' : htmlspecialchars($row['sub_category_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['dated']); ?></td>
                            <td><?php echo htmlspecialchars($row['document_no']); ?></td>
                            <td><a
                                    href="<?= $base_url ?>/view-posting.php?id=<?= htmlspecialchars($row['id']) ?>"><?php echo htmlspecialchars($row['title']); ?></a>
                            </td>
                            <td><a href="<?= $base_url ?>/view-posting.php?id=<?= htmlspecialchars($row['id']) ?>"
                                    class="btn btn-success btn-sm"><i class="ti ti-eye"></i></a>&nbsp;&nbsp;<a
                                    href="<?= $base_url ?>/edit-postings.php?id=<?= htmlspecialchars($row['id']) ?>"
                                    class="btn btn-info btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;
                                <button class="btn btn-danger btn-sm delete-post-button"
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