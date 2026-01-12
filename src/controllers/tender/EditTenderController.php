<?php
session_start();
require_once "../../database/Database.php";

class EditTenderController
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
                header('Location: ../../../edit-tenders.php?id=' . $_POST['tender_id']);
                exit;
            }

            if ($file_size > 1) {
                $_SESSION['error_message'] = "File size exceeds 1MB limit.";
                header('Location: ../../../edit-tenders.php?id=' . $_POST['tender_id']);
                exit;
            }

            $uploads_dir = $this->createDirectoryIfNotExists($path);
            $uniqueName = uniqid() . "_" . $_FILES[$pdf_attach]['name'];
            $file_path = "$uploads_dir/$uniqueName";

            if (move_uploaded_file($_FILES[$pdf_attach]["tmp_name"], '../../' . $file_path)) {
                return "$uploads_dir/$uniqueName";
            } else {
                return null; // Return null if upload fails
            }
        }
        return null;
    }

    public function updateTender()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../../manage-tenders.php');
            exit;
        }

        if (!isset($_POST['tender_id']) || empty($_POST['tender_id'])) {
            $_SESSION['error_message'] = "Tender ID is required.";
            header('Location: ../../../manage-tenders.php');
            exit;
        }

        $tender_id = $_POST['tender_id'];

        // Fetch current tender data
        $stmt = $this->pdo->prepare("SELECT * FROM tenders WHERE uniq_id = ? AND is_deleted='0'");
        $stmt->execute([$tender_id]);
        $currentTender = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentTender) {
            $_SESSION['error_message'] = "Tender not found.";
            header('Location: ../../../manage-tenders.php');
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

        // If errors exist, store them in session and redirect back to the form
        if (!empty($errors)) {
            $_SESSION['req_error_msg'] = $errors;
            $_SESSION['error_message'] = "Please fix the errors and try again.";
            header("Location: ../../../edit-tenders.php?id=" . $tender_id); // Redirect to form
            exit();
        }

        $financial_year = $_POST['financialYear'] ?? $currentTender['financial_year'];
        $tender_category = $_POST['tenderCategory'] ?? $currentTender['tender_category'];
        $tender_type = $_POST['tenderType'] ?? $currentTender['tender_type'];
        $tender_nit_ref_no = $_POST['nit_ref_no'] ?? $currentTender['tender_nit_ref_no'];
        $tender_dated = $_POST['dated'] ?? $currentTender['tender_dated'];
        $tender_posting = $_POST['datePosting'] ?? $currentTender['tender_posting'];
        $tender_title = $_POST['tender_title'] ?? $currentTender['tender_title'];
        $uniq_id = $currentTender['uniq_id'];

        // Handle file uploads - only update if new file uploaded
        $tender_notice_path = $this->pdfUpload('attachNotice', "uploads/Tenders/$financial_year/$uniq_id", ['pdf']) ?? $currentTender['tender_notice_path'];
        $tender_doc_path = $this->pdfUpload('attachDoc', "uploads/Tenders/$financial_year/$uniq_id", ['pdf']) ?? $currentTender['tender_doc_path'];
        $tender_other_attach_1_path = $this->pdfUpload('pdf_attachment1', "uploads/Tenders/$financial_year/$uniq_id", ['pdf', 'ppt', 'pptx', 'doc', 'docx']) ?? $currentTender['tender_other_attach_1_path'];
        $tender_other_attach_2_path = $this->pdfUpload('pdf_attachment2', "uploads/Tenders/$financial_year/$uniq_id", ['pdf', 'ppt', 'pptx', 'doc', 'docx']) ?? $currentTender['tender_other_attach_2_path'];
        $tender_other_attach_3_path = $this->pdfUpload('pdf_attachment3', "uploads/Tenders/$financial_year/$uniq_id", ['pdf', 'ppt', 'pptx', 'doc', 'docx']) ?? $currentTender['tender_other_attach_3_path'];

        $tender_other_attach_1_title = $_POST['pdf_attachment_title1'] ?? $currentTender['tender_other_attach_1_title'];
        $tender_other_attach_2_title = $_POST['pdf_attachment_title2'] ?? $currentTender['tender_other_attach_2_title'];
        $tender_other_attach_3_title = $_POST['pdf_attachment_title3'] ?? $currentTender['tender_other_attach_3_title'];

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "UPDATE tenders SET 
               tender_category = ?, 
               tender_title = ?, 
               financial_year = ?, 
               tender_type = ?, 
               tender_nit_ref_no = ?, 
               tender_dated = ?, 
               tender_posting = ?, 
               tender_notice_path = ?, 
               tender_doc_path = ?, 
               tender_other_attach_1_path = ?, 
               tender_other_attach_2_path = ?, 
               tender_other_attach_3_path = ?, 
               tender_other_attach_1_title = ?, 
               tender_other_attach_2_title = ?, 
               tender_other_attach_3_title = ?,
               updated_by = ?,
               updated_at = NOW()
               WHERE uniq_id = ?"
            );

            $stmt->execute([
                $tender_category,
                $tender_title,
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
                $_SESSION['username'],
                $tender_id
            ]);

            $this->pdo->commit();

            unset($_SESSION['post']);
            unset($_SESSION['req_error_msg']);

            $_SESSION['success_message'] = "Tender has been updated successfully.";
            header('Location: ../../../edit-tenders.php?id=' . $tender_id);
            exit;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            header('Location: ../../../edit-tenders.php?id=' . $tender_id);
            exit;
        }
    }

    public function softDelete()
    {
        session_start();

        $post = filter_input(INPUT_POST, 'ed');

        $stmt = $this->pdo->prepare("SELECT * FROM tenders WHERE uniq_id = :id AND is_deleted = '0'");
        $stmt->bindParam(':id', $post, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $sql = "UPDATE tenders SET is_deleted='1' WHERE uniq_id= :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':id', $post, PDO::PARAM_INT);

            $data = $stmt->execute();

            if ($data) {
                $_SESSION['message'] = "Tender deleted successfully.";
                header('Location: ../../../manage-tenders.php');
                exit;

            } else {
                $_SESSION['error_message'] = "Failed to delete data.";
                header('Location: ../../../manage-tenders.php');
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Tender Record not found!.";
            header('Location: ../../../manage-tenders.php');
            exit;
        }

    }
}

$controller = new EditTenderController();

if (isset($_POST['ed']) && $_POST['ed']) {
    $controller->softDelete();
} else {
    $controller->updateTender();
}

?>