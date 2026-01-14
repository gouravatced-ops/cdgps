<?php
$domainWhere = '';
$domainParams = [];
if ($domainId > 0) {
    $domainWhere = ' AND domain_id = ?';
    $domainParams = [$domainId];
}

$domainCount = getCount($pdo, 'domains', "is_deleted = '0'");
$catCount = getCount($pdo, 'category_master', "is_deleted = '0'" . $domainWhere, $domainParams);
$subCatCount = getCount($pdo, 'sub_category', "is_deleted = '0'" . $domainWhere, $domainParams);
$postCount = getCount($pdo, 'postings', "is_deleted = '0'");
$newsCount = getCount($pdo, 'news', "is_deleted = '0'" . $domainWhere, $domainParams);
$noticeCount = getCount($pdo, 'notices', "is_deleted = '0'" . $domainWhere, $domainParams);
$photoCount = getCount($pdo, 'albums', "is_deleted = '0'" . $domainWhere . ' AND type = ?', array_merge($domainParams, ['Photos']));
$vdoCount = getCount($pdo, 'albums', "is_deleted = '0'" . $domainWhere . ' AND type = ?', array_merge($domainParams, ['Videos']));
$pressCount = getCount($pdo, 'albums', "is_deleted = '0'" . $domainWhere . ' AND type = ?', array_merge($domainParams, ['Press Clips']));

// Assign consistent but varied colors for each card type
$cardColors = ['category' => '#6366f1',
'subcategory' => '#10b981',
'news' => '#ef4444',
'notices' => '#f59e0b',
'photos' => '#0ea5e9',
'videos' => '#8b5cf6',
'pressclips' => '#64748b',
'domains' => '#22c55e'
];

$todayThought = $thoughtsOfTheDay[array_rand($thoughtsOfTheDay) ];
/* Fetch password info for logged-in user */
$stmt = $pdo->prepare("
    SELECT password_set_date, password_expire_in_days 
    FROM users 
    WHERE id = :user_id 
    LIMIT 1
");
$stmt->execute([':user_id' => $_SESSION['user_id'] // adjust as per your session
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    $setDate = new DateTime($user['password_set_date']);
    $today = new DateTime();
    $interval = $setDate->diff($today);
    $daysPassed = (int)$interval->format('%a');
    $expireIn = (int)$user['password_expire_in_days'];
    $daysLeft = max(0, $expireIn - $daysPassed);
    $isExpired = ($daysLeft <= 0);
} else {
    $daysLeft = null;
    $isExpired = true;
}

// recent notices
$sql = "
    SELECT a.*, b.sub_category_name AS category_name, dm.eng_name, csc.child_sub_category_name
    FROM notices a
    JOIN sub_category b ON a.notice_subcategory = b.id
    LEFT JOIN domains dm ON dm.id = a.domain_id
    LEFT JOIN child_sub_category csc ON csc.id = a.notice_childsubcategory
    WHERE a.is_deleted = '0'
    " . ($domainId > 0 ? " AND a.domain_id = $domainId" : "") . "
    ORDER BY a.created_at DESC
    LIMIT 4
    ";
$recentNotices = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

function getCount(PDO $pdo, string $table, string $where = '1=1', array $params = []) {
    $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}
