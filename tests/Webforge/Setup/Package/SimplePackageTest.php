<?php

namespace Webforge\Setup\Package;

use Webforge\Setup\AutoLoadInfo;

class SimplePackageTest extends \Webforge\Code\Test\Base {
  
  protected $simplePackage;
  
  public function testAcceptanceBecauseIHaveNoIdeaWhatToTestHere() {
    $this->simplePackage = new SimplePackage('some-slug', $this->getTestDirectory()->sub('packages/ACME/'), new AutoLoadInfo(array()));
    
    $this->assertInstanceOf('Webforge\Setup\Package\Package', $this->simplePackage);
    $this->assertInstanceOf('Webforge\Setup\AutoLoadInfo', $this->simplePackage->getAutoLoadInfo());
  }
}
?>