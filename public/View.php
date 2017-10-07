<?php

class View {
  public function show($fileName, $data = null) {
    if ($data !== null) {
      $tweet = $data['tweet'];
      $r = $data['results'];
    }
    include(dirname(__FILE__) . "/{$fileName}");
  }
}
