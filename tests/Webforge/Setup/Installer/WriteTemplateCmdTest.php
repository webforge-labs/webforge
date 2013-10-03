<?php

namespace Webforge\Setup\Installer;

class WriteTemplateCmdTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\WriteTemplateCmd';
    parent::setUp();

    $this->output = $this->getMockbuilder('Webforge\Common\CommandOutput')->getMockForAbstractClass();
    
    $this->file = $this->getMock('Webforge\Common\System\File', array('writeContents'), array('somefile.txt'));
    
    $this->expectedContents = 'some contents';
    $this->tpl = $this->getMock('Webforge\Common\System\File', array('getContents'), array('somefile.txt'));
    $this->tpl->expects($this->any())->method('getContents')->will($this->returnValue('%which% contents'));
    
    $this->existingFile = $this->getMock('Webforge\Common\System\File', array(), array('existingFile.txt'));
    $this->existingFile->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
  }
  
  public function testCommandWritesContentsOfTemplateToFile() {
    $this->file->expects($this->once())->method('writeContents')->with($this->equalTo($this->expectedContents));
    
    $cmd = new WriteTemplateCmd($this->tpl, $this->file, array('which'=>'some'));
    $cmd->execute($this->output);
  }
  
  public function testDescribeIsComplete() {
    $cmd = new WriteTemplateCmd($this->tpl, $this->file, array('which'=>'some'));
    
    $this->assertContains((string) $this->tpl, $cmd->describe());
    $this->assertContains((string) $this->file, $cmd->describe());
  }

  public function testCommandWarnsIfFileExists() {
    $this->existingFile->expects($this->never())->method('writeContents');

    $cmd = new WriteTemplateCmd($this->tpl, $this->existingFile, array(), WriteCmd::IF_NOT_EXISTS);

    $this->output->expects($this->once())->method('warn');
    
    $cmd->execute($this->output);
  }
}
