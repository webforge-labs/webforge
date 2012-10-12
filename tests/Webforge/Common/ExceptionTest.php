<?php

namespace Webforge\Common;

class ExceptionTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->e = new Exception('this is the #1 exception', 0);
    
    $this->nested = new Exception('this is the #2 exception', 0, $this->e);
  }
  
  public function testExceptionTestContainsException1() {
    $text = $this->e->toString('text');
    $this->assertContains('this is the #1 exception', $text);
  }
  
  public function testExceptionTestContainsException1And2ForNested() {
    $text = $this->nested->toString('text');
    
    $this->assertContains('this is the #1 exception', $text);
    $this->assertContains('this is the #2 exception', $text);
  }
  
  public function testHierarchy() {
    $this->assertInstanceof('Exception', $this->e);
  }
  
  public function testMessageCanbeOverwritten() {
    $this->e->setMessage('blubb');
    $this->assertEquals('blubb', $this->e->getMessage());
    $this->assertNotContains('this is the #1 exception', $this->e->toString('text'));
  }
  
  public function testAppendMessageAddsTextToTheMessageToTheEnd() {
    $text = $this->e->appendMessage('. more detailed info.')->getMessage();
    
    $this->assertContains('this is the #1 exception. more detailed info.', $text);
  }

  public function testPrependMessageAddsTextToTheMessageAtTheBeginning() {
    $text = $this->e->prependMessage('[verbose info] ')->getMessage();
    
    $this->assertContains('[verbose info] this is the #1 exception', $text);
  }
  
}
?>