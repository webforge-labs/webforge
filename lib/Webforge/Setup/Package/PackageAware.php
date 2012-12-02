<?php

namespace Webforge\Setup\Package;

interface PackageAware {
  
  public function setPackage(Package $package);
  
  public function getPackage();
}
?>