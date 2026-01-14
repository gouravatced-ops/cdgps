<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/layouts/header.php'; 

$params = [];
$sql = "SELECT cm.*, p.file_path , dm.eng_name FROM albums cm INNER JOIN photos p on p.id = cm.cover_photo_id JOIN domains as dm ON dm.id = cm.domain_id WHERE cm.type='Press Clips' AND cm.is_deleted='0'";

if ($domainId > 0) {
    $sql .= " AND cm.domain_id = ?";
    $params[] = $domainId;
}
$sql .= " ORDER BY cm.created_at desc";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">

    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern">
                Manage Press Clips
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
                        <th>Album Title</th>
                        <th>Event Date</th>
                        <th style="white-space: nowrap;">Edit Album</th>
                        <th>Actions</th>
                        <th>Created</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1;
                    foreach ($categories as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['eng_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['name_en']); ?></td>
                            <td style="white-space: nowrap;"><?php echo htmlspecialchars(date("d-M-Y", strtotime($row['event_date']))); ?></td>
                            <td>
                                <a href="<?= $base_url ?>/edit-albums-details.php?album_id=<?php echo htmlspecialchars($row['uniq_id']) ?>" title="Edit Press Clip Details"
                                    class="btn btn-info btn-lg"><i class="ti ti-edit"></i></a>
                            <td style="white-space: nowrap;">
                                <a href="<?= $base_url ?>/edit-press-clips.php?album_id=<?php echo htmlspecialchars($row['uniq_id']) ?>" title="Add & Manage Press Clip"
                                    class="btn btn-primary btn-lg"><i class="ti ti-photo-edit"></i></a>&nbsp;&nbsp;
                                <button class="btn btn-danger btn-lg delete-photo-albums-button" title="Delete Press Clip"
                                    data-id="<?php echo htmlspecialchars($row['uniq_id']); ?>">
                                    <i class="ti ti-trash"></i>
                                </button>
                                <a href="<?= $base_url ?>/view-press-clips-album.php?album_id=<?php echo htmlspecialchars($row['uniq_id']) ?>" title="View Press Clip"
                                    class="btn btn-warning btn-lg"><i class="ti ti-eye"></i></a>
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