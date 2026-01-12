<?php

class SubCategoryModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertSubCategory($subCatName, $categoryId, $createdBy)
    {
        $sql = "INSERT INTO sub_category (sub_category_name, category_id, created_by) VALUES (:subCatName, :categoryId, :createdBy)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':subCatName', $subCatName, PDO::PARAM_STR);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':createdBy', $createdBy, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function insertChildSubCategory($chsubCatName, $hnSubCatName, $description, $subcategoryId, $categoryId, $createdBy)
    {
        $sql = "INSERT INTO child_sub_category (child_sub_category_name, hn_child_sub_category_name, subcategory_id, category_id, created_by) VALUES (:childSubCatName, :hnChildSubCatName, :subCategoryId, :categoryId, :createdBy)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':childSubCatName', $chsubCatName, PDO::PARAM_STR);
        $stmt->bindParam(':hnChildSubCatName', $hnSubCatName, PDO::PARAM_STR);
        // $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':subCategoryId', $subcategoryId, PDO::PARAM_INT);
        $stmt->bindParam(':createdBy', $createdBy, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getAllCategories()
    {
        $sql = "SELECT id, category_name FROM category_master WHERE is_active = 1";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubCategory($cat_id)
    {
        $sql = "SELECT id, sub_category_name FROM sub_category WHERE is_active = 1 AND category_id= '$cat_id'";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateSubCategory($id, $engCat, $updatedBy)
    {
        $sql = "UPDATE sub_category SET
                    sub_category_name = :sub_category_name,
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':sub_category_name', $engCat, PDO::PARAM_STR);
        $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateChildSubCategory($id, $engCat, $hnCat, $catId, $subCatId, $updatedBy)
    {
        $sql = "UPDATE child_sub_category SET
                    category_id = :category_id,
                    subcategory_id = :subcategory_id,
                    child_sub_category_name = :child_sub_category_name,
                    hn_child_sub_category_name = :hn_child_sub_category_name,
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':child_sub_category_name', $engCat, PDO::PARAM_STR);
        $stmt->bindParam(':hn_child_sub_category_name', $hnCat, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $catId, PDO::PARAM_INT);
        $stmt->bindParam(':subcategory_id', $subCatId, PDO::PARAM_INT);
        $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function softDeleteSubCategory($id, $updatedBy)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE sub_category SET
                    is_deleted = '1',
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
            $stmt->execute();

            $sql1 = "UPDATE postings SET
                    sub_category = NULL,
                    last_updated_on = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE sub_category = :id";
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt1->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
            $stmt1->execute();
            $this->pdo->commit();

            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // print_r(throw $e);
            // die('sd');
            return false;
        }
    }

    public function softDeleteChildSubCategory($id, $updatedBy)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE child_sub_category SET
                    is_deleted = '1',
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();

            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            
            return false;
        }
    }
}

?>