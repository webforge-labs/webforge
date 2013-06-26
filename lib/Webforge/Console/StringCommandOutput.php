<?php

namespace Webforge\Console;

/**
 */
class StringCommandOutput implements CommandOutput {

  protected $msgs;

  /**
   * A success message that is printed highlighted and should indicate a successful event
   */
  public function ok($msg) {
    $this->msgs .= 'ok: '.$msg."\n";
    return $this;
  }

  public function toString() {
    return $this->msgs;
  }
}
