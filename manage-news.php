<?php

/**
 * Manage News Page
 * Protected page with session check
 */
require_once __DIR__ . '/src/helpers/session_helper.php';
requireLogin(); // This will redirect if not logged in or session expired

require_once __DIR__ . '/src/database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$sql = "SELECT cm.*, dm.eng_name FROM news cm LEFT JOIN domains dm ON dm.id = cm.domain_id WHERE cm.is_deleted='0' ORDER BY cm.created_at desc";

$categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">

    <div class="card">
        <div class="card-body p-0">
            <div class="card-header-modern">
                Manage News
            </div>

            <div class="p-3">
                <!-- rest form / content -->
            </div>
            <div class="mt-1">
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
                            <th style="white-space: nowrap;">Session Year</th>
                            <th>Title</th>
                            <th>News Date</th>
                            <th>Actions</th>
                            <th>Hide/Unhide</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1;
                        foreach ($categories as $row): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?= $row['eng_name'] ?></td>
                                <td><?= $row['session_year'] ?></td>
                                <td><?php echo htmlspecialchars($row['news_title']); ?></td>
                                <td style="white-space: nowrap;"><?php echo htmlspecialchars(date("d-M-Y", strtotime($row['news_event_date']))); ?></td>
                                </td>
                                <td style="white-space: nowrap;"><a href="<?= $base_url ?>/edit-news.php?id=<?php echo htmlspecialchars($row['uniq_id']) ?>"
                                        class="btn btn-info btn-lg" title="Edit News"><i class="ti ti-edit"></i></a>&nbsp;&nbsp;
                                    <button class="btn btn-danger btn-lg" title="Delete News"
                                        onclick="deleteNews(<?php echo htmlspecialchars($row['uniq_id']); ?>, 'dn', '')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-<?= $row['is_hide'] == 'Y' ? 'success' : 'warning' ?> btn-lg"
                                        title="<?= $row['is_hide'] == 'Y' ? 'Unhide' : 'Hide' ?> News"
                                        onclick="deleteNews(<?php echo htmlspecialchars($row['uniq_id']); ?>, 'hn', '<?= $row['is_hide'] == 'Y' ? 'Unhide' : 'Hide' ?>' )">
                                        <i class="ti ti-<?= $row['is_hide'] == 'Y' ? 'link' : 'unlink' ?>"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <script>
        function deleteNews(photoId, type1, hide = '') {
            let actionType = type1 === 'dn' ? 'deleteNews' : 'hideNews';
            if (!confirm("Are you sure you want to " + (type1 === 'dn' ? 'Delete' : hide) + " this News?")) {
                return;
            }
            $("#loader").show();
            $.ajax({
                url: '<?= $base_url ?>/src/controllers/news/insertNews.php',
                type: 'POST',
                data: {
                    ed: photoId,
                    action: actionType
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    window.location.reload();
                    $("#loader").hide();
                    console.error('Error deleting photo:', error);
                }
            });
        }
    </script>

    <?php require_once __DIR__ . '/layouts/footer.php'; ?>