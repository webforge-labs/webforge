<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\File;

class WriteCmdTest extends \Webforge\Code\Test\Base {
  
  protected $existingFile;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\WriteCmd';
    parent::setUp();
    
    $this->contents = 'some contents';
    $this->file = $this->getMock('Webforge\Common\System\File', array('writeContents'), array('somefile.txt'));
    $this->existingFile = $this->getMock('Webforge\Common\System\File', array(), array('existingFile.txt'));
    $this->existingFile->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
  }
  
  public function testCommandWritesContentsToFile() {
    $this->file->expects($this->once())->method('writeContents')->with($this->equalTo($this->contents));
    
    $cmd = new WriteCmd($this->contents, $this->file);
    $cmd->execute();
  }
  
  public function testDescribeIsComplete() {
    $cmd = new WriteCmd($this->contents, $this->file);
    
    $this->assertContains((string) $this->file, $cmd->describe());
  }
  
  public function testCommandWarnsIfFileExists() {
    $this->existingFile->expects($this->never())->method('writeContents');

    $cmd = new WriteCmd($this->contents, $this->existingFile, WriteCmd::IF_NOT_EXISTS);
    
    $subscriber = $this->getMock('Psc\Code\Event\Subscriber');
    $subscriber->expects($this->once())->method('trigger');
    $cmd->subscribe($subscriber, WriteCmd::WARNING);
    
    $cmd->execute();
  }
}
?>