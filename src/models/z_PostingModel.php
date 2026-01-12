<?php

class PostingModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function generateUniqueSixDigitID()
    {
        do {
            $id = mt_rand(100000, 999999);

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM postings WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $exists = $stmt->fetchColumn();
        } while ($exists > 0);
        return $id;
    }

    function generatePostingId()
    {
        $currentYear = date('y');
        $currentMonth = date('m');

        $counter = 1;

        $stmt = $this->pdo->query("SELECT MAX(id) AS last_posting_id FROM postings");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastPostingId = $result['last_posting_id'];

        if ($lastPostingId !== false) {
            $lastMonth = substr($lastPostingId, 2, 2);
            if ($lastMonth == $currentMonth) {
                $lastCounter = intval(substr($lastPostingId, -3));
                $counter = $lastCounter + 1;
            }
        }
        $formattedCounter = str_pad($counter, 3, '0', STR_PAD_LEFT);
        $postingId = $currentYear . $currentMonth . $formattedCounter;
        return $postingId;
    }

    public function saveData($data)
    {
        $sql = "INSERT INTO postings (
                    id, type, category, sub_category, document_no, dated,
                    reference_no, reference_date, title, attachment,
                    new_flag, new_no_of_days, status, ip_address, created_on, last_updated_on
                ) VALUES (
                    :id, :type, :category, :sub_category, :document_no, :dated,
                    :reference_no, :reference_date, :title, :attachment,
                    :new_flag, :new_no_of_days, :status, :ip_address,
                    CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmt->bindParam(':type', $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(':category', $data['category'], PDO::PARAM_INT);
        $stmt->bindParam(':sub_category', $data['sub_category'], PDO::PARAM_INT);
        $stmt->bindParam(':document_no', $data['document_no'], PDO::PARAM_STR);
        $stmt->bindParam(':dated', $data['dated'], PDO::PARAM_STR);
        $stmt->bindParam(':reference_no', $data['reference_no'], PDO::PARAM_STR);
        $stmt->bindParam(':reference_date', $data['reference_date'], PDO::PARAM_STR);
        $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindParam(':attachment', $data['attachment'], PDO::PARAM_STR);
        $stmt->bindParam(':new_flag', $data['new_flag'], PDO::PARAM_STR);
        $stmt->bindParam(':new_no_of_days', $data['new_no_of_days'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $data['ip_address'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function fetchSubCategoriesByCategoryId($categoryId)
    {
        $stmt = $this->pdo->prepare("SELECT id, sub_category_name FROM sub_category WHERE category_id = :categoryId AND is_deleted = '0'");
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryName($catId)
    {
        $stmt = $this->pdo->prepare("SELECT category_name FROM category_master WHERE id = :categoryId AND is_deleted = '0'");
        $stmt->bindParam(':categoryId', $catId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubCategoryName($subcatId)
    {
        $stmt = $this->pdo->prepare("SELECT sub_category_name FROM sub_category WHERE id = :subcategoryId AND is_deleted = '0'");
        $stmt->bindParam(':subcategoryId', $subcatId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePost($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM postings WHERE id = :id AND is_deleted = '0'");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($data) {
            $sql = "UPDATE postings SET is_deleted='1' WHERE id= :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
    }

    public function deleteAttach($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM postings WHERE id = :id AND is_deleted = '0'");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($data) {
            $sql = "UPDATE postings SET attachment=null WHERE id= :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    public function restorePost($id)
    {

        $stmt = $this->pdo->prepare("SELECT * FROM postings WHERE id = :id AND is_deleted = '1'");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM category_master WHERE id = :id AND is_deleted = '1'");
        $stmt->bindParam(':id', $data[0]['category'], PDO::PARAM_INT);
        $stmt->execute();
        $exists = $stmt->fetchColumn();
       
        if ($exists > 0) {
            return 'no_cat';
        }

        if ($data) {
            $sql = "UPDATE postings SET is_deleted='0' WHERE id= :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    public function getType($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM sy_fy WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
