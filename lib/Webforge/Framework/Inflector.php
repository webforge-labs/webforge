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

  public function commandNameit($className) {
    if (Preg::match($className, '/^[A-Z0-9]+$/')) {
      return mb_strtolower($className);
    }
    
    $specials = preg_quote(implode("", array('.','@','\\',' ','[',']','(',')')), '/');
    
    $dashed = Preg::replace(
      // in
      $className,
      // what
      sprintf('/%s|[%s]/',
        "(?<=\w)([A-Z]|[0-9])",
        $specials
      ),
      // with
      '-\\1'
    );

    $dashed = mb_strtolower($dashed);
    
    return $dashed;
  }
}
