<?php

namespace Webforge\Code;

class ClassNotFoundException extends \Psc\Exception {
  
  public static function fromFQN($fqn) {
    return new static(sprintf("The Class '%s' cannot be found", $fqn));
  }
}
?>