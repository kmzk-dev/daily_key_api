<?php
header("Access-Control-Allow-Origin: *");
require_once '../private_config/config.php';

// --- パラメータ設定 ---
$length = isset($_GET['length']) ? (int)$_GET['length'] : 15;
if ($length > 15) {
    $length = 15;
}
if ($length < 1) {
    $length = 1;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'alphanumeric';

// --- 乱数生成 ---
// 1. 日付を取得
date_default_timezone_set('Asia/Tokyo');
define("SECRET_KEY","");
$daily_seed = date('Y-m-d'); // 例: 2025-06-10
$secure_seed = hash('sha256', SECRET_KEY . $daily_seed);

// 3. 安全なシードを基に乱数生成器を初期化
mt_srand(crc32($secure_seed)); // crc32はハッシュ文字列を整数に変換するために使用
if ($type === 'numeric') {
    // 数字のみ
    $chars = '0123456789';
} else {
    // 英数字（デフォルト）
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
}

// 5. 乱数を生成
$randomString = '';
$charLength = strlen($chars);
for ($i = 0; $i < $length; $i++) {
    $randomString .= $chars[mt_rand(0, $charLength - 1)];
}

// --- レスポンス ---
// 有効期限（24時間）を設定
date_default_timezone_set('Asia/Tokyo'); // 念のため設定を再確認
$now = new DateTime();
$tomorrow_midnight = new DateTime('tomorrow 00:00:00', new DateTimeZone('Asia/Tokyo'));
$expires_in_seconds = $tomorrow_midnight->getTimestamp() - $now->getTimestamp();

header('Pragma: public');
header('Cache-Control: max-age=' . $expires_in_seconds . ', must-revalidate'); // must-revalidateを追加すると、キャッシュが期限切れになった際に必ずオリジンサーバーに再検証を求めます
header('Expires: ' . gmdate('D, d M Y H:i:s', $tomorrow_midnight->getTimestamp()) . ' GMT');

// JSON形式で出力
header('Content-Type: application/json');
echo json_encode([
    'random_string' => $randomString,
    'generated_at_jst' => $daily_seed . ' 00:00:00',
    'expires_at_jst' => date('Y-m-d', strtotime('+1 day')) . ' 00:00:00',
    'length' => $length,
]);

exit;