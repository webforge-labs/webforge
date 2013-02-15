<?php

namespace Webforge\Framework\Package;

use Webforge\Common\Util;

class PackageNotFoundException extends \Psc\Exception {
  
  public static function fromSearch(Array $searchCriteria, Array $prefixes) {
    return new static(sprintf("Cannot find package for search: %s.\nPackage prefixes avaible: %s", Util::varInfo($searchCriteria), implode(', ',$prefixes)));
  }
}
?>