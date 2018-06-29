<?php
require_once __DIR__ . '/vendor/autoload.php';

use Anonymization\Anonymization\Anonymous as Anonymous;

$a = new Anonymous();
$a->start();
