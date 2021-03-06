<?php
require_once('../PowerToys.php');
$pt = new PHP_Powertoys();

//PEAR系
$obj = $pt->getPearI('Net_POP3');
$obj = $pt->getPear('Net/POP3.php');

//変数/配列系
$a['a'] = 'a';
$a['b'] = 'b';
$a['c'] = 'c';

$a = $pt->arrayKeyReplace($a, 'b', 'd');
$pt->save_var_dump($a, 'var_dump.txt');
$pt->print_r_ex($a);

//UTF-8系
$bom = $pt->checkBom('sample.txt');
if($bom){
	echo 'BOM付きです';
}

$str = file_get_contents('sample.txt');
$str = $pt->removeBom($str);
$bom = $pt->checkBom($str);
if(!$bom){
	echo 'BOMなしです';
}

//画像系
$img = $pt->iopen(dirname(dirname(__FILE__)) . '/sample/sample.jpg');
$img = $pt->iopen(dirname(dirname(__FILE__)) . '/sample/sample.png');
$img = $pt->iopen(dirname(dirname(__FILE__)) . '/sample/sample.gif');

//オブジェクト系
$pt2 = $pt->objectClone($pt);

//ファイル取得
echo $pt2->file_get_contents_ex('http://openpear.org/');
?>