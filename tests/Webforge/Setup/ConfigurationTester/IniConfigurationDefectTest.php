<?php

namespace Webforge\Setup\ConfigurationTester;

/**
 * @group class:Webforge\Setup\IniConfigurationDefect
 */
class IniConfigurationDefectTest extends \Psc\Code\Test\Base {
  
  protected $iniConfigurationDefect;
  
  public function setUp() {
    $this->chainClass = 'Webforge\Setup\IniConfigurationDefect';
    parent::setUp();
    $this->iniConfigurationDefect = new IniConfigurationDefect(
      $name = 'post_max_size',
      '10M', 10*1024*1024,
      '2M', 2*1024*1024,
      '=='
    );
  }
  
  public function testToStringHasAVerboseMessage() {
    $msg = (string) $this->iniConfigurationDefect;
    
    $this->assertContains('[ini]: it\'s wrong that post_max_size', $msg);
    $this->assertContains('10M', $msg);
    $this->assertContains('2M', $msg);
  }
}
?>