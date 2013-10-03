<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\File;

class CopyCmdTest extends \Webforge\Code\Test\Base {
  
  protected $existingFile;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CopyCmd';
    parent::setUp();

    $this->output = $this->getMockbuilder('Webforge\Common\CommandOutput')->getMockForAbstractClass();
    
    $this->existingFile = File::createTemporary();
    $this->existingFile->writeContents('not empty');
    
    $this->destination =
      $this->getMockBuilder('Webforge\Common\System\File')
           ->setMethods(array('copy'))
           ->setConstructorArgs(array(__DIR__.DIRECTORY_SEPARATOR.'destination.file'))
           ->disableArgumentCloning()
           ->getMock();

    $this->someDir =
      $this->getMockBuilder('Webforge\Common\System\Dir')
           ->setMethods(array('copy'))
           ->setConstructorArgs(array(__DIR__.DIRECTORY_SEPARATOR.'destination.dir/'))
           ->disableArgumentCloning()
           ->getMock();
           
    $this->someFile = new File(__FILE__);
    
    $this->source = $this->getMock('Webforge\Common\System\File', array('copy'), array(__FILE__));    
  }
  
  public function testCopiesFileFromFileToFile() {
    $this->source->expects($this->once())->method('copy')->with($this->equalTo($this->destination));
    
    $copy = new CopyCmd($this->source, $this->destination);
    $this->execute($copy);
  }
  
  public function testCopiesFilesOnlyIfTheyDontExistsWhenFlagIsSet() {
    $this->source->expects($this->never())->method('copy');
    
    $copy = new CopyCmd($this->source, $destination = $this->existingFile, CopyCmd::IF_NOT_EXISTS);
    
    $this->output->expects($this->once())->method('warn');
    
    $this->execute($copy);
  }
  
  public function testDescribeIsComplete() {
    $copy = new CopyCmd($this->source, $this->destination);
    
    $this->assertContains((string) $this->destination, $copy->describe());
    $this->assertContains((string) $this->source, $copy->describe());
  }
  
  public function testThrowsInvalidArgumentExceptionWhenDirIsCopiedToFile() {
    $this->someDir->expects($this->never())->method('copy');
    
    $this->setExpectedException('InvalidArgumentException');
    $copy = new CopyCmd($this->someDir, $this->someFile);
    
    $this->execute($copy);
  }

  protected function execute($command) {
    return $command->execute($this->output);
  }
}
