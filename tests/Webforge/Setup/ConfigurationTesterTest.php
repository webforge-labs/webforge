<?php

namespace Webforge\Setup;

use Psc\A;

/**
 * @group class:Webforge\Setup\ConfigurationTester
 */
class ConfigurationTesterTest extends \Psc\Code\Test\Base {
  
  protected $t, $retriever;
  
  public function setUp() {
    $this->chainClass = 'Webforge\Setup\ConfigurationTester';
    parent::setUp();
    $this->retriever = $this->getMock('Webforge\Setup\ConfigurationRetriever', array('retrieveIni'));
    $this->t = new ConfigurationTester(NULL, $this->retriever);
    
    $fakeIni = array(
      'mbstring.internal_encoding'=>'jp18',
      'post_max_size'=>'2M',
      'display_errors'=>'On',
      'register_globals'=>'0'
    );
    
    $this->retriever->expects($this->any())->method('retrieveIni')->will($this->returnCallback(function ($name) use ($fakeIni) {
      return $fakeIni[$name];
    }));
  }
  
  // tested ob der configurationTester lokal inis holen kann (sein Default)
  public function testAcceptance_ConfigurationTesterRetrievesLokalIniValues() {
    $t = new ConfigurationTester();
    $t->INI('post_max_size', ini_get('post_max_size'));
    
    $this->assertZeroDefects();
  }
  
  public function testWrongIniValuesAreGatheredWithinDefects() {
    $this->t->INI('mbstring.internal_encoding', 'utf-8');
    
    $defects = $this->t->getDefects();
    $this->assertCount(1, $defects);
    $this->assertInstanceOf('Webforge\Setup\IniConfigurationDefect', $iniDefect = $defects[0]);
    $this->assertEquals('jp18', $iniDefect->getActualValue());
    $this->assertEquals('utf-8', $iniDefect->getExpectedValue());
  }
  
  public function testMegabytesGetNormalizedAndCanBeComparedToBytes() {
    $this->t->INI('post_max_size',2*1024*1024);
    $this->t->INI('post_max_size','2M');
    $this->t->INI('post_max_size','2M', '>=');
    $this->t->INI('post_max_size',1024, '<');
    
    $this->assertZeroDefects();
  }
  
  public function testBooleansGetNormalized() {
    $this->t->INI('display_errors', TRUE);
    $this->t->INI('register_globals', FALSE);
    
    $this->assertDefects(0);
  }

  public function testBooleansGetNormalizedCmpInt() {
    $this->t->INI('display_errors', 1);
    $this->t->INI('register_globals', 0);
    
    $this->assertDefects(0);
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