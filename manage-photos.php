<?php

// Display all errors, warnings, and notices (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/src/database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$sql = "SELECT cm.*, p.file_path FROM albums cm INNER JOIN photos p on p.id = cm.cover_photo_id WHERE cm.type='Photos' AND cm.is_deleted='0' ORDER BY cm.created_at desc";

$categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">

    <div class="card">
        <div class="card-header bg-success">
            <h5 class="text-white">Manage Photos</h5>
        </div>
        <div class="card-body">
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
                        <th>Album Title</th>
                        <th>Event Date</th>
                        <th>Edit Album</th>
                        <th>Action</th>
                        <th>Created</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($categories as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['name_en']); ?></td>
                            <td style="white-space: nowrap;"><?php echo htmlspecialchars(date("d-M-Y", strtotime($row['event_date']))); ?></td>
                            </td>
                            <td>
                                <a href="<?= $base_url ?>/edit-albums-details.php?album_id=<?php echo htmlspecialchars($row['uniq_id']) ?>"
                                    title="Edit Photo Album Details" class="btn btn-info btn-lg"><i
                                        class="ti ti-edit"></i></a>
                            <td style="white-space: nowrap;">
                                <a href="<?= $base_url ?>/edit-photos.php?album_id=<?php echo htmlspecialchars($row['uniq_id']) ?>"
                                    title="Add & Manage Photos" class="btn btn-primary btn-lg"><i
                                        class="ti ti-photo-edit"></i></a>&nbsp;&nbsp;
                                <button class="btn btn-danger btn-lg delete-photo-albums-button" title="Delete Photo Album"
                                    data-id="<?php echo htmlspecialchars($row['uniq_id']); ?>">
                                    <i class="ti ti-trash"></i>
                                </button>
                                <a href="<?= $base_url ?>/view-photos-album.php?album_id=<?php echo htmlspecialchars($row['uniq_id']) ?>"
                                    title="View Photo Album" class="btn btn-warning btn-lg"><i class="ti ti-eye"></i></a>
                            </td>
                            <td style="white-space: nowrap;"><?= $row['created_at'] ?></td>
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