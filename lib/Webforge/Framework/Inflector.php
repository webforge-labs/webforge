<?php

namespace Webforge\Framework;

use Webforge\Common\Preg;

class Inflector {

  /**
   * @return string
   */
  public function namespaceify($string) {
    return ucfirst(Preg::replace_callback($string, '/\-([a-zA-Z])/', function ($match) {
      return mb_strtoupper($match[1]);
    }));
  }
}
?>