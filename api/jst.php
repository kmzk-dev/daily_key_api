<?php
header("Access-Control-Allow-Origin: *");
require_once '../private_config/config.php';

// --- Parameters ---
$length = isset($_GET['length']) ? (int)$_GET['length'] : 15;
if ($length > 15) { $length = 15; }
if ($length < 1) { $length = 1; }

$type = isset($_GET['type']) ? $_GET['type'] : 'alphanumeric';
$extra_seed = isset($_GET['extra']) ? $_GET['extra'] : '';

// --- initialize ---
date_default_timezone_set('Asia/Tokyo');
$daily_seed = date('Y-m-d');
$secure_seed = hash('sha256', SECRET_KEY . $daily_seed . $extra_seed);
mt_srand(crc32($secure_seed)); // crc32でハッシュ文字列を整数に変換
if ($type === 'numeric') {
    // 数字のみ出力
    $chars = '0123456789';
} else {
    // 英数字を出力:デフォルト
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
}

// 5. 乱数を生成
$randomString = '';
$charLength = strlen($chars);
for ($i = 0; $i < $length; $i++) {
    $randomString .= $chars[mt_rand(0, $charLength - 1)];
}

$current_jst_datetime = date('Y-m-d H:i:s');

// --- Cache ---
date_default_timezone_set('Asia/Tokyo');
$now = new DateTime();
$tomorrow_midnight = new DateTime('tomorrow 00:00:00', new DateTimeZone('Asia/Tokyo'));
$expires_in_seconds = $tomorrow_midnight->getTimestamp() - $now->getTimestamp();

header('Pragma: public');
header('Cache-Control: max-age=' . $expires_in_seconds . ', must-revalidate');
header('Expires: ' . gmdate('D, d M Y H:i:s', $tomorrow_midnight->getTimestamp()) . ' GMT');

// --- JSON Response ---
header('Content-Type: application/json');
echo json_encode([
    'random_string' => $randomString,
    'generated_at_jst' => $current_jst_datetime,
    'expires_at_jst' => date('Y-m-d', strtotime('+1 day')) . ' 00:00:00',
    'length' => $length,
]);

exit;