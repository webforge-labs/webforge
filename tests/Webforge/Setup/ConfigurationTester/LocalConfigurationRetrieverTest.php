<?php

namespace Webforge\Setup\ConfigurationTester;

class LocalConfigurationRetrieverTest extends \Webforge\Code\Test\Base {
  
  protected $localConfigurationRetriever;
  
  public function setUp() {
    $this->chainClass = 'Webforge\Setup\LocalConfigurationRetriever';
    parent::setUp();
    $this->localConfigurationRetriever = new LocalConfigurationRetriever();
  }
  
  public function testAcceptance() {
    $this->localConfigurationRetriever->retrieveIni('post_max_size', ini_get('post_max_size'));
    $this->localConfigurationRetriever->retrieveIni('include_path', ini_get('include_path'));
  }
  
  public function testToStringIsNotEmpty() {
    $this->assertNotEmpty((string) $this->localConfigurationRetriever);
  }
}
