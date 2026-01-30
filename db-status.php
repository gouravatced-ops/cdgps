<?php
ob_start();

require './src/database/Database.php';
require './system-config.php';

/* fallback base url */
$base_url = $base_url ?? '/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['retry'])) {
    try {
        new Database(); // retry connection
        header("Location: " . $base_url);
        exit;
    } catch (Exception $e) {
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? 'Database Status'; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= $faviconIcon ?? ''; ?>" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont;
            overflow: hidden;
        }

        /* Subtle animated background */
        .bg-svg {
            position: fixed;
            inset: 0;
            z-index: 0;
            opacity: 0.12;
        }

        /* Card */
        .status-card {
            position: relative;
            z-index: 2;
            background: #f1f3f5;
            border-radius: 14px;
            border: 1px solid #dee2e6;
            color: #212529;
        }

        .icon-wrap {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: #fff3cd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            border: 1px solid #ffe69c;
        }

        .icon-wrap i {
            color: #997404;
        }

        .status-title {
            color: #1b263b;
        }

        .status-text {
            color: #6c757d;
        }

        .btn-admin {
            border-color: #adb5bd;
            color: #1b263b;
        }

        .btn-admin:hover {
            background: #1b263b;
            color: #fff;
            border-color: #1b263b;
        }

        .pulse {
            animation: pulse 2.4s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, .35);
            }

            70% {
                box-shadow: 0 0 0 22px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }
    </style>
</head>

<body>

    <!-- 🔹 Subtle SVG wave -->
    <svg class="bg-svg" viewBox="0 0 1440 600" preserveAspectRatio="none">
        <path fill="#ffc107">
            <animate attributeName="d" dur="12s" repeatCount="indefinite"
                values="
            M0,320 C240,300 480,340 720,320 960,300 1200,340 1440,320 L1440,600 L0,600 Z;
            M0,300 C240,340 480,300 720,300 960,340 1200,300 1440,300 L1440,600 L0,600 Z;
            M0,320 C240,300 480,340 720,320 960,300 1200,340 1440,320 L1440,600 L0,600 Z
            " />
        </path>
    </svg>

    <!-- 🔹 Content -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="status-card shadow p-4 text-center" style="max-width:440px;width:100%;">

            <div class="icon-wrap pulse">
                <i class="bi bi-database-exclamation fs-1"></i>
            </div>

            <h4 class="fw-semibold status-title mt-3">
                Database Not Connected
            </h4>

            <p class="status-text mt-2">
                Unable to establish database connection.<br>
                Please verify configuration or contact system administrator.
            </p>

            <form method="post" class="d-flex gap-2 justify-content-center mt-4">
                <button type="submit"
                    name="retry"
                    value="1"
                    class="btn btn-outline-secondary btn-admin btn-sm">
                    <i class="bi bi-arrow-clockwise"></i> Retry
                </button>

                <a href="mailto:admin@example.com"
                    class="btn btn-outline-secondary btn-admin btn-sm">
                    <i class="bi bi-envelope"></i> Contact Admin
                </a>
            </form>

            <small class="d-block text-muted mt-4">
                Status Code: DB_CONN_FAILED
            </small>

        </div>
    </div>

</body>

</html>