<?php

namespace Webforge\Framework\Package;

interface PackageAware {
  
  public function setPackage(Package $package);
  
  public function getPackage();
}
?>