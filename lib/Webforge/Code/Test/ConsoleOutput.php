<?php

namespace Webforge\Code\Test;

class ConsoleOutput extends \Symfony\Component\Console\Output\Output {
  
  public $stream;
  
  public function doWrite($msg, $newline) {
    $this->stream .= $msg.($newline ? "\n" : '');
  }
}
?>