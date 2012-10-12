<?php

namespace Webforge\Setup;

class MissingConfigVariableException extends \Webforge\Common\Exception {
  
  public $keys;
  
  public static function fromKeys(Array $keys) {
    $e = new static(sprintf("the config variable '%s' cannot be found.", implode('.', $keys)));
    $e->keys = $keys;
    return $e;
  }
}
?>