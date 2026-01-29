<?php

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }

    require_once __DIR__ . '/layouts/header.php';

    $sql = "SELECT * FROM sy_fy";

    $syfy = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern d-flex align-items-center justify-content-between">
                Manage Sessions
                    <a href="<?= $base_url ?>/create-session.php" class="btn btn-warning btn-sm">
                        <strong>+ Create</strong>
                    </a>
            </div>

            <div class="p-2">
                <!-- rest form / content -->
            </div>

            <table id="syFyTable" class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Type</th>
                        <th>Calendar Year</th>
                        <th>Financial Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($syfy as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo ($row['type'] == 'sy' ? 'Session Year' : 'Financial Year'); ?></td>
                            <td><?php echo htmlspecialchars($row['calender_year']); ?></td>
                            <td><?php echo htmlspecialchars(empty($row['financial_year']) ? 'N/A' : $row['financial_year']); ?>
                            </td>
                            <td><a href="<?= $base_url ?>/edit-session.php?id=<?= htmlspecialchars($row['id']) ?>"
                                    class="btn btn-primary btn-sm"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;
                                <button class="btn btn-danger btn-sm delete-session-button"
                                    data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                            <!-- <td><a href="<?= $base_url ?>/edit-session.php"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;<a href="<?= $base_url ?>/delete-session.php"><i class="ti ti-trash"></i></a></td> -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>