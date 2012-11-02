<?php

namespace Webforge\Setup;

/**
 */
class ConfigurationTest extends \Webforge\Code\Test\Base {
  
  protected $configuration;
  
  public function setUp() {
    $conf = array(
      'webforge.staging'=>true,
      'webforge.dev'=>false,
      'webforge.location'=>'D:\www\ka',
      'doctrine.entities'=>array('user','project','customer'),
      'root'=>'thv'
    );
    
    $this->configuration = new Configuration($conf);
  }
  
  public function testGetReturnsTheValueForTheKey() {
    $this->assertEquals('D:\www\ka', $this->configuration->get('webforge.location'));
    $this->assertEquals(array('user','project','customer'), $this->configuration->get('doctrine.entities'));
    $this->assertEquals('thv', $this->configuration->get('root'));
  }

  /**
   * @expectedException Webforge\Setup\MissingConfigVariableException
   */
  public function testMissingConfigException() {
    $this->configuration->req('thiskeydoesnotexist');
  }
  
  /**
   * @depends testMissingConfigException
   */
  public function testKeysExceptionForNonDott() {
    try {
      $this->configuration->req('thiskeydoesnotexist');
      
    } catch (MissingConfigVariableException $e) {
      $this->assertEquals(array('thiskeydoesnotexist'),$e->keys);
      return;
    }
    
    $this->fail('Exception not cought');
  }

  /**
   * @depends testMissingConfigException
   */
  public function testKeysExceptionForDott() {
    try {
      $this->configuration->req('thiskey.doesnotexist');
      
    } catch (MissingConfigVariableException $e) {
      $this->assertEquals(array('thiskey','doesnotexist'),$e->keys);
      return;
    }
    
    $this->fail('Exception not cought');
  }
}
?>