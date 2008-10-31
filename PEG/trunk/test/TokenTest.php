<?php
include_once dirname(__FILE__) . '/t/t.php';

$t = new lime_test(null, new lime_output_color);

$token = token('hoge');
$context = new PEG_Context('hogehoge');
$t->is($token->parse($context), 'hoge');
$t->is($context->tell(), 4);
$t->is($token->parse($context), 'hoge');

try {
    $token->parse($context);
    $t->fail();
} catch (PEG_Failure $e) {
}

