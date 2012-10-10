<?php

namespace Webforge\Code;

class ClassFileNotFoundException extends \Psc\Exception {
  
  public static function fromFQN($fqn) {
    return new static(sprintf("The File for the class '%s' cannot be found", $fqn));
  }
}
?>