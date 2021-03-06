<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(4, new lime_output_color);

$input = array(array("abc","123","AtoZ"),
               array("234","xyz","1to9"),
               );

/* ============================== */

$lime->diag("checking option: target");

$it = new Text_CsvReader_Filter_AutoIncrement(new ArrayIterator($input),
                                              array('target'=>0));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(1,"123","AtoZ"), '0th element');
$lime->ok($output[1] === array(2,"xyz","1to9"), '1st element');

/* ============================== */

$lime->diag("Exception: no option");

try {
  $it = new Text_CsvReader_Filter_AutoIncrement(new ArrayIterator($input));
  $lime->fail('required parameter not specified.');
}
catch (CsvReaderException $e) {
  $lime->pass('required parameter not specified.');
}
