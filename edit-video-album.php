<?php
session_start();
if ((isset($_SESSION['login'])) && ($_SESSION['login'] == true)) {

    require_once __DIR__ . '/src/database/Database.php';

    $database = new Database();
    $pdo = $database->getConnection();

    $albumId = isset($_GET['album_id']) ? $_GET['album_id'] : null;

    if (!$albumId) {
        die('Invalid request: missing album ID.');
    }

    $albumQuery = $pdo->prepare('SELECT a.* FROM albums a WHERE a.uniq_id = ? AND a.is_deleted=0');
    $albumQuery->execute([$albumId]);
    $album = $albumQuery->fetch(PDO::FETCH_ASSOC);

    $album_id = $album['id'];

    $videosQuery = $pdo->prepare('SELECT * FROM videos WHERE albums_id = ? ');
    $videosQuery->execute([$album_id]);
    $videos = $videosQuery->fetchAll(PDO::FETCH_ASSOC);

    if (!$album) {
        die('Album not found or unable to fetch videos.');
    }

    // Check if form data is stored in session
    $form_data = $_SESSION['form_data'] ?? [];

    $title = "Admin - Post Video Album";

    require_once __DIR__ . '/layouts/header.php';
    ?>

    <script src="https://cdn.ckeditor.com/4.9.2/standard/ckeditor.js"></script>
    <script>
        setTimeout(function () {
            $('.cke_notifications_area').remove();
        }, 1000);
    </script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="text-primary text-center">Manage Video Album</h3>
            </div>
            <div class="card-body">
                <div id="msg"></div>

                <!-- Manage Existing Videos -->
                <div id="VideoList">
                    <div class="row">

                        <?php $setIndex = 1;
                        foreach ($videos as $Video) {
                            $videoId = htmlspecialchars($Video['id']);
                            $videoLink = htmlspecialchars(trim($Video['video_link']), ENT_QUOTES, 'UTF-8');
                            $videoCaptionEn = htmlspecialchars($Video['video_title'], ENT_QUOTES, 'UTF-8');
                            // $videoCaptionHn = htmlspecialchars($Video['hn_video_title'], ENT_QUOTES, 'UTF-8');
                    
                            echo '<div class="col-md-4">
                                    <div class="card">
                                    <div class="card-body">';

                            if ($album['cover_video_id'] == $videoId) {
                                echo '<h6 class="card-title text-center text-success">Cover Video</h6>';
                            }
                            echo '<div class="Video-row" id="Video-' . $videoId . '">
                                                <iframe width="auto" height="160"
                                                    src="' . $videoLink . '"
                                                    title="' . $videoCaptionEn . '"
                                                    frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    allowfullscreen>
                                                </iframe>
                                                <textarea class="form-control mt-2" placeholder="Enter Video Link" pattern="https?://.+" >' . $videoLink . '</textarea>
                                                <textarea class="form-control mt-2" placeholder="Enter Video Link Caption" >' . $videoCaptionEn . '</textarea>
                                            
                                                <div class="d-flex align-items-center mt-2">
                                                    <button class="btn btn-danger me-2" onclick="deleteVideo(\'Video-' . $videoId . '\')">Delete</button>
                                                    <button class="btn btn-success me-2" onclick="updateVideo(\'Video-' . $videoId . '\')">Update</button> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                            $setIndex++;
                        } ?>
                    </div>
                </div>

                <!-- Add New Video -->
                <div class="card">
                    <div class="card-header bg-success text-light">
                        <h4>Add New Video Link</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="url" pattern="https?://.+" id="newVideoInput" class="form-control"
                                        placeholder="Enter Youtube Link">
                                    <input type="text" id="newVideoCaptionEn" class="form-control mt-2"
                                        placeholder="Enter caption for new Video">
                                    <div id="newVideoPreview" class="mt-2"></div>
                                    <div id="newVideoError" class="error-message"></div>
                                    <button class="btn btn-primary mt-2" onclick="addNewVideo()">Add Video</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php

    $embed_script = "albumForm.js";

    require_once __DIR__ . '/layouts/footer.php';

    unset($_SESSION['form_data']);
} else {
    echo "Invalid session, <a href='index.php'>click here</a> to login";
}
?>