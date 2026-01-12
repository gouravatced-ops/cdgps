<?php

class CategoryModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function insertCategory($domainCatId, $engCat, $hinCat, $createdBy)
    {
        $sql = "INSERT INTO category_master (domain_id, category_name, hindi_category_name, created_by) VALUES (:domainCatId, :engCat, :hinCat, :createdBy)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':domainCatId', $domainCatId, PDO::PARAM_STR);
        $stmt->bindParam(':engCat', $engCat, PDO::PARAM_STR);
        $stmt->bindParam(':hinCat', $hinCat, PDO::PARAM_STR);
        $stmt->bindParam(':createdBy', $createdBy, PDO::PARAM_STR);

        return $stmt->execute();
    }
    public function getAllCategories()
    {
        $sql = "SELECT * FROM category_master";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateCategory($id, $domainId, $engCat, $hnCat, $updatedBy)
    {
        $sql = "UPDATE category_master SET
                    domain_id = :domainId,
                    category_name = :category,
                    hindi_category_name = :hincategory,
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':domainId', $domainId, PDO::PARAM_INT);
        $stmt->bindParam(':category', $engCat, PDO::PARAM_INT);
        $stmt->bindParam(':hincategory', $hnCat, PDO::PARAM_INT);
        $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function softDeleteCategory($id, $updatedBy)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM postings WHERE category = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $exists = $stmt->fetchColumn();

        if ($exists > 0) {
            return 'post_available';
        }

        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE category_master SET
                    is_deleted = '1',
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
            $stmt->execute();

            $sql = "UPDATE sub_category SET
                    is_deleted = '1',
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE category_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
            $stmt->execute();

            // $sql = "UPDATE postings SET
            //         is_deleted = '1',
            //         last_updated_on = CURRENT_TIMESTAMP,
            //         updated_by = :updated_by
            //     WHERE category = :id";
            // $stmt = $this->pdo->prepare($sql);
            // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            // $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
            // $stmt->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

?>