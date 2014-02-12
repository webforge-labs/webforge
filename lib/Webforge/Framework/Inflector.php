<?php

namespace Webforge\Framework;

use Webforge\Common\Preg;
use Webforge\Common\String as S;

class Inflector {

  /**
   * @return string
   */
  public function namespaceify($string) {
    return S::dashToCamelCase($string);
  }

  public function commandNameit($className) {
    return S::camelCaseToDash($className);
  }
}
