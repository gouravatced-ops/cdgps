<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/src/database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

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

require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">

    <div class="row">

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-category.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-success text-light text-center">
                        <h6 class="fw-semibold fs-4">Total Category</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $catCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-sub-category.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-primary text-light text-center">
                        <h6 class="fw-semibold fs-4">Total Sub Category</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $subCatCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-postings.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-success text-info text-center">
                        <h6 class="fw-semibold fs-4">Total Postings</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $postCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-news.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-success text-light text-center">
                        <h6 class="fw-semibold fs-4">Total News</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $newsCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-notices.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-primary text-light text-center">
                        <h6 class="fw-semibold fs-4">Total Notice</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $noticeCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-photos.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-success text-info text-center">
                        <h6 class="fw-semibold fs-4">Total Photos</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $photoCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-videos.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-success text-info text-center">
                        <h6 class="fw-semibold fs-4">Total Videos</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $vdoCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="<?= $base_url ?>/manage-press-clips.php">
                <div class="card overflow-hidden rounded-2">
                    <div class="card-header bg-success text-info text-center">
                        <h6 class="fw-semibold fs-4">Total Press Clip</h6>
                    </div>
                    <div class="card-body pt-3 p-4">
                        <div class="align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0"><?= $pressCount ?></h6>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>