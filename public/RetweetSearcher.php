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

  public function start($url) {
    $id = $this->__getIdFromUrl($url);
    $this->tweet = $this->__getTweet($id);
    $this->__loop();
    return [
      'tweet' => $this->tweet,
      'results' => $this->results
    ];
  }

  private function __getIdFromUrl($url) {
    $urlArray = explode("/", $url);
    return array_pop($urlArray);
  }

  private function __getTweet($id) {
    return $this->connection->get("statuses/show", [
      'id' => $id
    ]);
  }

  private function __loop() {
    do {
      $this->lastIdPrev = $this->lastId;
      $content = $this->__get_retweets($this->tweet, $this->lastId);
      foreach ($content->statuses as $status) {
        array_push($this->results, $status);
      }
      if (count($content->statuses) > 0) {
        $lastStatus = array_pop($content->statuses);
        $this->lastId = $lastStatus->id_str;
      }
    } while ($this->lastId !== $this->lastIdPrev);
  }

  private function __get_retweets($tweet, $maxId) {
    $text = $tweet->text;
    $user = $tweet->user;
    $createdAt = date('Y-m-d', strtotime($tweet->created_at));

    // 改行を含む場合は最初の改行までで検索
    foreach (["\r\n", "\n", "\r"] as $br) {
      if (strpos($text, $br) !== false) {
        $text = substr($text, 0, strpos($text, $br));
      }
    }

    return $this->connection->get("search/tweets", [
      'q' => "{$text} @{$user->screen_name} since:{$createdAt} filter:nativeretweets",
      'count' => 100,
      'result_type' => 'mixed',
      'include_entities' => false,
      'max_id' => $maxId
    ]);
  }
}
