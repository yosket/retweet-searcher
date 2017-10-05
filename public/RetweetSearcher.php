<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class RetweetSearcher {

  private $access_token = '';
  private $access_token_secret = '';
  private $text = '';
  private $lastId = '999999999999999999';
  private $results = [];

  public function __construct($access_token, $access_token_secret) {
    $this->access_token = $access_token;
    $this->access_token_secret = $access_token_secret;
    $this->connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $this->access_token, $this->access_token_secret);
  }

  public function start($text) {
    $this->text = $text;
    $this->__loop();
    $this->__show('template.html');
  }

  private function __loop() {
    do {
      $this->lastIdPrev = $this->lastId;
      $content = $this->__get_retweets($this->text, $this->lastId);
      foreach ($content->statuses as $status) {
        array_push($this->results, $status);
      }
      $lastStatus = array_pop($content->statuses);
      $this->lastId = $lastStatus->id_str;
    } while ($this->lastId !== $this->lastIdPrev);
  }

  private function __get_retweets($text, $maxId) {
    return $this->connection->get("search/tweets", [
      'q' => "$text filter:nativeretweets",
      'count' => 100,
      'result_type' => 'mixed',
      'include_entities' => false,
      'max_id' => $maxId
    ]);
  }

  private function __show($fileName) {
    $text = $this->text;
    $r = $this->results;
    include(dirname(__FILE__) . "/{$fileName}");
  }
}
