<?php

namespace Webforge\Common;

class Exception extends \Psc\Exception {
  
  public function toString($format = 'text') {
    return self::getExceptionText($this, $format);
  }
}
?>