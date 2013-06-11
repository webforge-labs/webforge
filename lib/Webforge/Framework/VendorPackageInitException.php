<?php

namespace Webforge\Framework;

use Webforge\Common\System\Dir;

class VendorPackageInitException extends Exception {

  public static function fromIdentifierAndVendor($identifier, Dir $vendor, \Exception $previous = NULL) {
    return new static(
      sprintf(
        "Package with identifier '%s' cannot be loaded from local vendor directory %s\n".
        "Are all dependencies installed with composer? Did you added this dependency to your composer config?", 
        $identifier, $vendor
      ),
      0,
      $previous
    );
  }
}
