<?php

namespace Webforge\Setup\ConfigurationTester;

use Psc\A;

/**
 * @group class:Webforge\Setup\ConfigurationTester\ConfigurationFileTester
 */
class ConfigurationFileTesterTest extends \Webforge\Code\Test\Base {
  
  protected $t, $retriever;
  protected $fileTester, $jsonFile;
  
  public function setUp() {
    $this->chainClass = 'Webforge\Setup\ConfigurationTester\ConfigurationTester';
    parent::setUp();
    $this->retriever = $this->getMock('Webforge\Setup\ConfigurationTester\ConfigurationRetriever', array('retrieveIni'));
    $this->t = new ConfigurationTester($this->retriever);
    
    $fakeIni = array(
      'mbstring.internal_encoding'=>'jp18',
      'post_max_size'=>'2M',
      'display_errors'=>'On',
      'register_globals'=>'0'
    );
    
    $this->retriever->expects($this->any())->method('retrieveIni')->will($this->returnCallback(function ($name) use ($fakeIni) {
      return $fakeIni[$name];
    }));
    
    $this->jsonFile = $this->getFile('php-ini-configuration.json', 'Setup/');
    
    $this->fileTester = new ConfigurationFileTester(
      $this->jsonFile,
      $this->t
    );
  }
  
  public function testAcceptance_ConfigurationTesterTestsCorrectly() {
    $this->fileTester->process();
    
    $this->assertZeroDefects();
  }
  
  public function testCreateForLocal() {
    $fileTester = ConfigurationFileTester::create($this->jsonFile, ConfigurationFileTester::LOCAL);
    
    $this->assertInstanceOf('Webforge\Setup\ConfigurationTester\LocalConfigurationRetriever',
                            $fileTester->getConfigurationTester()->getRetriever()
                            );
  }

  public function testCreateForRemote() {
    $fileTester = ConfigurationFileTester::create($this->jsonFile, ConfigurationFileTester::REMOTE, 'http://localhost:80');
    
    $this->assertInstanceOf('Webforge\Setup\ConfigurationTester\RemoteConfigurationRetriever',
                            $fileTester->getConfigurationTester()->getRetriever()
                            );
  }

  public function testAuthenticationSetting() {
    $retriever = $this->getMock('RemoteConfigurationRetriever', array('setAuthentication'), array('/fake/url'));
    
    $retriever->expects($this->once())->method('setAuthentication')
              ->with($this->equalTo('theuser'), $this->equalTo('asecret'))->will($this->returnSelf());
                    
    $fileTester = new ConfigurationFileTester(
      $this->jsonFile,
      new ConfigurationTester($retriever)
    );
    
    $fileTester->setAuthentication('theuser','asecret');
  }

  
  protected function assertZeroDefects() {
    return $this->assertDefects(0);
  }
  
  protected function assertDefects($cnt) {
    $this->assertCount($cnt, $this->t->getDefects(), $cnt.' defects expected. Defects-List:'."\n".$this->debugDefects());
  }
  
  protected function debugDefects() {
    return A::join($this->t->getDefects(), "%s\n");
  }
}
?>