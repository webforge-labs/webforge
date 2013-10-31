<?php

namespace Webforge\Framework\Package;

use Webforge\Common\Util;

class PackageNotFoundException extends \Webforge\Common\Exception {
  
  public static function fromSearch(Array $searchCriteria, Array $prefixes) {
    return new static(sprintf("Cannot find package for search: %s.\nPackage prefixes available: %s", Util::varInfo($searchCriteria), implode(', ',$prefixes)));
  }
}
