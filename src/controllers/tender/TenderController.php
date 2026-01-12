<?php
session_start();
require_once "../../database/Database.php";

class TenderController
{
    private $pdo;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['login'] == 0) {
            header('Location: ../../../index.php');
            exit;
        }

        $database = new Database();
        $this->pdo = $database->getConnection();

    }

    private function generateUniqueTenderID($length = 6)
    {
        do {
            $newsID = substr(str_shuffle(str_repeat('0123456789', $length)), 0, $length);
        } while ($this->newsIDExists($newsID));
        return $newsID;
    }

    private function newsIDExists($newsID)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM tenders WHERE uniq_id = ?");
        $stmt->execute([$newsID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    private function createDirectoryIfNotExists($baseDir1)
    {
        $baseDir = '../../' . rtrim($baseDir1, '/') . '/';
        $uploadsDir = "$baseDir";
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        return "$baseDir1";
    }

    private function pdfUpload($pdf_attach, $path, $allowedFile)
    {
        if (!isset($_FILES[$pdf_attach]) || $_FILES[$pdf_attach]['error'] == UPLOAD_ERR_NO_FILE) {
            return null; // No file uploaded, return null
        }

        if ($_FILES[$pdf_attach]["name"] != '') {
            $allowed_extensions = $allowedFile;
            $file_ext = pathinfo($_FILES[$pdf_attach]['name'], PATHINFO_EXTENSION);
            $file_size = $_FILES[$pdf_attach]['size'] / 1024 / 1024; // Convert to MB

            if (!in_array(strtolower($file_ext), $allowed_extensions)) {
                $_SESSION['error_message'] = "Invalid file type.";
                header('Location: ../../../create-tender.php');
                exit;
            }

            if ($file_size > 1) {
                $_SESSION['error_message'] = "File size exceeds 1MB limit.";
                header('Location: ../../../create-tender.php');
                exit;
            }

            $uploads_dir = $this->createDirectoryIfNotExists($path);
            $uniqueName = uniqid() . "_" . $_FILES[$pdf_attach]['name'];
            $file_path = "$uploads_dir/$uniqueName";

            // die($file_path);
            // move_uploaded_file($_FILES[$pdf_attach]["tmp_name"], $file_path);
            // return "$uploads_dir/$uniqueName";

            // Move file to destination
            if (move_uploaded_file($_FILES[$pdf_attach]["tmp_name"], '../../' . $file_path)) {
                return "$uploads_dir/$uniqueName";
            } else {
                return null; // Return null if upload fails
            }

        }
        return null;
    }

    public function insertTender()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../../create-tender.php');
            exit;
        }

        $errors = [];
        $_SESSION['post'] = $_POST; // Store previous input values

        // Example validation
        if (empty($_POST['financialYear'])) {
            $errors['financialYear'] = "Financial Year is required";
        }

        if (empty($_POST['tenderCategory'])) {
            $errors['tenderCategory'] = "Tender Category is required";
        }

        if (empty($_POST['nit_ref_no'])) {
            $errors['nit_ref_no'] = "NIT/Ref No is required";
        }

        if (!isset($_FILES['attachNotice']) || $_FILES['attachNotice']['error'] == UPLOAD_ERR_NO_FILE) {
            $_SESSION['error_message'] = "Attach Tender Notice is mandatory.";
            header("Location: ../../../create-tender.php"); // Redirect to form
            exit();
        }

        // If errors exist, store them in session and redirect back to the form
        if (!empty($errors)) {
            $_SESSION['req_error_msg'] = $errors;
            $_SESSION['error_message'] = "Please fix the errors and try again.";
            header("Location: ../../../create-tender.php"); // Redirect to form
            exit();
        }


        $uniqueTenderID = $this->generateUniqueTenderID();

        $financial_year = $_POST['financialYear'] ?? null;
        $tender_category = $_POST['tenderCategory'] ?? null;
        $tender_type = $_POST['tenderType'] ?? null;
        $tender_nit_ref_no = $_POST['nit_ref_no'] ?? null;
        $tender_dated = $_POST['dated'] ?? null;
        $tender_posting = $_POST['datePosting'] ?? null;
        $tender_title = $_POST['tender_title'] ?? null;

        $tender_notice_path = $this->pdfUpload('attachNotice', "uploads/Tenders/$financial_year/$uniqueTenderID", ['pdf']);

        if ($tender_notice_path) {
            $tender_doc_path = $this->pdfUpload('attachDoc', "uploads/Tenders/$financial_year/$uniqueTenderID", ['pdf']);

            $tender_other_attach_1_path = $this->pdfUpload('pdf_attachment1', "uploads/Tenders/$financial_year/$uniqueTenderID", ['pdf', 'ppt', 'pptx', 'doc', 'docx']);

            $tender_other_attach_2_path = $this->pdfUpload('pdf_attachment2', "uploads/Tenders/$financial_year/$uniqueTenderID", ['pdf', 'ppt', 'pptx', 'doc', 'docx']);

            $tender_other_attach_3_path = $this->pdfUpload('pdf_attachment3', "uploads/Tenders/$financial_year/$uniqueTenderID", ['pdf', 'ppt', 'pptx', 'doc', 'docx']);

            $tender_other_attach_1_title = $_POST['pdf_attachment_title1'];

            $tender_other_attach_2_title = $_POST['pdf_attachment_title2'];

            $tender_other_attach_3_title = $_POST['pdf_attachment_title3'];
            // if (!$financial_year || !$tender_category || !$tender_type || !$tender_nit_ref_no || !$tender_dated || !$tender_posting || !$tender_title || !$tender_notice_path) {
            //     $_SESSION['error_message'] = "All fields are required.";
            //     header('Location: ../../../create-tender.php');
            //     exit;
            // }

            try {
                $this->pdo->beginTransaction();

                $stmt = $this->pdo->prepare(
                    "INSERT INTO tenders (uniq_id, tender_category, tender_title, financial_year, tender_type, 
                tender_nit_ref_no, tender_dated, tender_posting, tender_notice_path, tender_doc_path, tender_other_attach_1_path, tender_other_attach_2_path, tender_other_attach_3_path, tender_other_attach_1_title, tender_other_attach_2_title, tender_other_attach_3_title, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $uniqueTenderID,
                    $tender_category,
                    trim($tender_title),
                    $financial_year,
                    $tender_type,
                    $tender_nit_ref_no,
                    $tender_dated,
                    $tender_posting,
                    $tender_notice_path,
                    $tender_doc_path,
                    $tender_other_attach_1_path,
                    $tender_other_attach_2_path,
                    $tender_other_attach_3_path,
                    $tender_other_attach_1_title,
                    $tender_other_attach_2_title,
                    $tender_other_attach_3_title,
                    $_SESSION['username']
                ]);
                $this->pdo->commit();

                unset($_SESSION['post']);
                unset($_SESSION['req_error_msg']);

                $_SESSION['success_message'] = "Tender has been posted successfully.";
                header('Location: ../../../create-tender.php');
                exit;
            } catch (Exception $e) {
                $this->pdo->rollBack();
                $_SESSION['error_message'] = "Error: " . $e->getMessage();
                header('Location: ../../../create-tender.php');
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Something went wrong, Please try Again or Contact Admin.";
            header("Location: ../../../create-tender.php"); // Redirect to form
            exit();
        }

    }
}

$controller = new TenderController();
$controller->insertTender();
?>