<?php

namespace Webforge\Console;

/**
 */
class StringCommandOutput implements CommandOutput {

  protected $msgs;

  /**
   * @inherit-doc
   */
  public function ok($msg) {
    $this->msgs .= 'ok: '.$msg."\n";
    return $this;
  }

  /**
   * @inherit-doc
   */
  public function warn($msg) {
    $this->msgs .= 'warning: '.$msg."\n";
    return $this;
  }

  /**
   * @inherit-doc
   */
  public function msg($msg) {
    $this->msgs .= $msg."\n";
    return $this;
  }

  public function toString() {
    return $this->msgs;
  }
}
