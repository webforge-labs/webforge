<?php

namespace Webforge\Setup\Package;

use Webforge\Common\Util;

class PackageNotFoundException extends \Psc\Exception {
  
  public static function fromSearch(Array $searchCriteria) {
    return new static(sprintf('Cannot find package for search: %s', Util::varInfo($searchCriteria)));
  }
}
?>