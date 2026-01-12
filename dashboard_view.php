<?php

/**
 * Protected page with session check
 */
require_once __DIR__ . '/src/helpers/session_helper.php';
requireLogin(); // This will redirect if not logged in or session expired

require_once __DIR__ . '/src/database/Database.php';

include('./src/utils/utlis.php');

$database = new Database();
$pdo = $database->getConnection();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM domains WHERE is_deleted='0'");
$stmt->execute();
$commrCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM category_master WHERE is_deleted='0'");
$stmt->execute();
$catCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sub_category WHERE is_deleted='0'");
$stmt->execute();
$subCatCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM postings WHERE is_deleted='0'");
$stmt->execute();
$postCount = $stmt->fetchColumn();


$stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE is_deleted='0'");
$stmt->execute();
$newsCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notices WHERE is_deleted='0'");
$stmt->execute();
$noticeCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM albums WHERE is_deleted='0' and type='Photos'");
$stmt->execute();
$photoCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM albums WHERE is_deleted='0' and type='Videos'");
$stmt->execute();
$vdoCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM albums WHERE is_deleted='0' and type='Press Clips'");
$stmt->execute();
$pressCount = $stmt->fetchColumn();

// Assign consistent but varied colors for each card type
$cardColors = [
    'category' => '#6366f1',      // Indigo
    'subcategory' => '#10b981',   // Emerald
    'news' => '#ef4444',          // Red
    'notices' => '#f59e0b',       // Amber
    'photos' => '#0ea5e9',        // Sky
    'videos' => '#8b5cf6',        // Purple
    'pressclips' => '#64748b',    // Slate
    'domains' => '#22c55e'        // Green
];

$todayThought = $thoughtsOfTheDay[array_rand($thoughtsOfTheDay)];

/* Fetch password info for logged-in user */
$stmt = $pdo->prepare("
    SELECT password_set_date, password_expire_in_days 
    FROM users 
    WHERE id = :user_id 
    LIMIT 1
");

$stmt->execute([
    ':user_id' => $_SESSION['user_id'] // adjust as per your session
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {

    $setDate   = new DateTime($user['password_set_date']);
    $today    = new DateTime();
    $interval = $setDate->diff($today);

    $daysPassed = (int) $interval->format('%a');
    $expireIn   = (int) $user['password_expire_in_days'];

    $daysLeft  = max(0, $expireIn - $daysPassed);
    $isExpired = ($daysLeft <= 0);
} else {
    $daysLeft  = null;
    $isExpired = true;
}


require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row g-3">
        <!-- Time & Thought -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap dashboard-header">
                    <div>
                        <h4 class="mb-0 fw-semibold" id="liveTime">--:--</h4>
                        <small class="text-muted" id="liveDate">Loading...</small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Thought of the Day</small>
                        <span class="fw-medium fst-italic" id="thoughtText">
                            "<?= $todayThought; ?>"
                        </span>
                    </div>
                </div>

                <?php if ($isExpired == false && $daysLeft < 10) { ?>
                    <div class="system-alert-footer animate-attention">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-alert-triangle"></i>
                            <span>
                                Your password will expire in <strong><?= $daysLeft; ?> days</strong>.
                                Please reset your password before expiry.
                                <strong>Failure to do so will require reset through the System Administrator.</strong>
                                <a href="<?= $base_url ?>/update-password.php">Reset now</a>
                            </span>

                        </div>
                        <?php if ($daysLeft > 5) { ?>
                            <button class="alert-close" onclick="this.parentElement.remove()">
                                <i class="ti ti-x"></i>
                            </button>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>
<div class="container-fluid mt-4">
    <div class="row">

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-category.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2 text-center" style="color:white !important; background : <?= $cardColors['category']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total Category</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $catCount ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-category fs-3" style="color : <?= $cardColors['category']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-sub-category.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2 text-center" style="color:white !important; background : <?= $cardColors['subcategory']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total Sub Category</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $subCatCount ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-layout-list fs-3" style="color : <?= $cardColors['subcategory']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-news.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2 text-center" style="color:white !important; background : <?= $cardColors['news']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total News</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $newsCount ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-news fs-3" style="color : <?= $cardColors['news']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-notices.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2 text-center" style="color:white !important; background : <?= $cardColors['notices']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total Notice</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $noticeCount ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-file-text fs-3" style="color : <?= $cardColors['notices']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-photos.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2 text-info text-center" style="color:white !important; background : <?= $cardColors['photos']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total Photos</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $photoCount ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-photo fs-3" style="color : <?= $cardColors['photos']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-videos.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2 p-2 text-info text-center" style="color:white !important; background : <?= $cardColors['videos']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total Videos</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $vdoCount ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-video fs-3" style="color : <?= $cardColors['videos']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-press-clips.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2  text-info text-center" style="color:white !important; background : <?= $cardColors['pressclips']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total Press Clip</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $pressCount ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-clipboard fs-3" style="color : <?= $cardColors['pressclips']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="<?= $base_url ?>/manage-commr.php">
                <div class="card overflow-hidden rounded-2 shadow-sm">
                    <div class="card-header p-2 text-center" style="color:white !important; background : <?= $cardColors['domains']; ?>">
                        <h6 class="fw-semibold" style="font-size: 22px;" style="color:white !important;">Total Domains</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2">
                        <div>
                            <h2 class="mb-0" style="font-size: 22px;"><?= $commrCount ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-1">
                            <i class="ti ti-world fs-3" style="color : <?= $cardColors['domains']; ?>"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<script>
    /* Live Time & Date */
    function updateTime() {
        const now = new Date();
        document.getElementById('liveTime').innerText =
            now.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });

        document.getElementById('liveDate').innerText =
            now.toLocaleDateString(undefined, {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>