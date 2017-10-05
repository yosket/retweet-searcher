<?php

require "vendor/autoload.php";
require_once 'settings.php';
require_once 'RetweetSearcher.php';

function debug($val) {
  echo '<pre>';
  var_dump($val);
  echo '</pre>';
}

$retweetSearcher = new RetweetSearcher($access_token, $access_token_secret);
$retweetSearcher->start($searchText);
