<?php

class DomainsModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function insertDomains($engName, $hinName, $domainPath, $domainAbout, $createdBy)
    {
        $sql = "INSERT INTO domains (eng_name, hin_name, domain_path, about, created_by) VALUES (:engName, :hinName, :domainPath, :domainAbout, :createdBy)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':engName', $engName, PDO::PARAM_STR);
        $stmt->bindParam(':hinName', $hinName, PDO::PARAM_STR);
        $stmt->bindParam(':domainPath', $domainPath, PDO::PARAM_STR);
        $stmt->bindParam(':domainAbout', $domainAbout, PDO::PARAM_STR);
        $stmt->bindParam(':createdBy', $createdBy, PDO::PARAM_STR);

        return $stmt->execute();
    }
    public function getAllDomains()
    {
        $sql = "SELECT * FROM domains";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateDomains($id, $engName, $hinName, $domainPath, $domainAbout, $updatedBy)
    {
        $sql = "UPDATE domains SET
                    eng_name = :engName,
                    hin_name = :hinName,
                    domain_path = :domainPath,
                    about = :domainAbout,
                    updated_date = CURRENT_TIMESTAMP,
                    updated_by = :updated_by
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':engName', $engName, PDO::PARAM_INT);
        $stmt->bindParam(':hinName', $hinName, PDO::PARAM_INT);
        $stmt->bindParam(':domainPath', $domainPath, PDO::PARAM_STR);
        $stmt->bindParam(':domainAbout', $domainAbout, PDO::PARAM_STR);
        $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function softDeleteDomains($id, $updatedBy)
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

            $sql = "UPDATE domains SET
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
            throw $e;
        }
    }
}
