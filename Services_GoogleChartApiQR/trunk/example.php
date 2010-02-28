<?php
/**
 * example.php
 *
 */

require_once 'Services/GoogleChartApiQR.php';

$qr = new Services_GoogleChartApiQR(200,200);
$qr->setChoe('UTF-8');
$qr->setForceEncode(true);
$str    = "google chart api qr codes." . "\r\n"
        . "テスト";
$qr->setChl($str);

try {
    $view = $qr->view();
    $data = $qr->create();
} catch (Exception $e) {
    echo $e->getMessage();
}
echo "<img src=\"$view\">";
$fp = fopen('sample.jpg', "w");
fwrite($fp, $data);


