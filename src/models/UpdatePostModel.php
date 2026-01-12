<?php
class UpdatePostModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function updatePosting($data)
    {
        $sql = "UPDATE notices SET
                    notice_subcategory = :sub_category,
                    notice_dated = :notice_dated,
                    notice_ref_no = :notice_ref_no,
                    notice_title = :notice_title,
                    notice_path = :attachment,
                    notice_new_tag = :notice_new_tag,
                    notice_new_tag_days = :notice_new_tag_days,
                    status = :status,
                    ip_address = :ip_address,
                    session_year = :session_year,
                    updated_by = :updated_by,
                    notice_url = :external_url,
                    url_tab_open = :url_tab_open
                WHERE uniq_id = :uniq_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':uniq_id', $data['uniq_id'], PDO::PARAM_INT);
        $stmt->bindParam(':sub_category', $data['sub_category'], PDO::PARAM_INT);
        $stmt->bindParam(':notice_dated', $data['notice_dated'], PDO::PARAM_STR);
        $stmt->bindParam(':notice_ref_no', trim($data['notice_ref_no']), PDO::PARAM_STR);
        $stmt->bindParam(':notice_title', trim($data['notice_title']), PDO::PARAM_STR);
        $stmt->bindParam(':attachment', $data['attachment'], PDO::PARAM_STR);
        $stmt->bindParam(':notice_new_tag', trim($data['notice_new_tag']), PDO::PARAM_STR);
        $stmt->bindParam(':notice_new_tag_days', trim($data['notice_new_tag_days']), PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $data['ip_address'], PDO::PARAM_STR);
        $stmt->bindParam(':session_year', $data['session_year'], PDO::PARAM_STR);
        $stmt->bindParam(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':external_url', $data['external_url'], PDO::PARAM_STR);
        $stmt->bindParam(':url_tab_open', $data['new_tab_open'], PDO::PARAM_STR);
        return $stmt->execute();
    }
}
