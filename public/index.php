<?php

date_default_timezone_set('Asia/Tokyo');

require "vendor/autoload.php";
require_once 'settings.php';
require_once 'RetweetSearcher.php';
require_once 'View.php';

function debug($val) {
  echo '<pre>';
  var_dump($val);
  echo '</pre>';
}

if (isset($_POST['url']) && $_POST['url']) {
  $retweetSearcher = new RetweetSearcher($access_token, $access_token_secret);
  $response = $retweetSearcher->start($_POST['url']);
  $view = new View();
  $view->show('result.html', $response);
} else {
  $view = new View();
  $view->show('template.html');
}
