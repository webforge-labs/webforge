<?php

namespace Webforge\Code\Test;

use Webforge\Setup\Installer\PartsInstaller;
use Webforge\Setup\Installer\Macro;
use Webforge\Setup\Installer\Command;
use Webforge\Framework\Container;
use Webforge\Common\System\Dir;
use org\bovigo\vfs\vfsStream;

class InstallerPartTestCase extends MacroTestCase {
  
  protected $installer;
  protected $container;
  
  protected $output;
  
  public function setUp() {
    parent::setUp();
    
    $packageRoot = vfsStream::setup('package-root');
    vfsStream::copyFromFileSystem((string) $this->getTestDirectory()->sub('packages/ACMESuperBlog/'), $packageRoot, $maxFileSize = 2*1024*1024);
    $this->target = new Dir(vfsStream::url('package-root').'/');

    $this->container = new Container();
    $this->webforge = $this->container->getPackageRegistry()->findByDirectory(Dir::factoryTS(__DIR__));
    
    /*
     all parts installer tests are operating on ACMESuperBlog (which is a vfs directory root)
     in webforge tests $this->package->getIdentifier() === 'acme/super-blog'
    */
    $this->package = $this->container->getPackageRegistry()->addComposerPackageFromDirectory($this->target);
    $this->container->setLocalPackage($this->package); // inject
    
    $this->output = $this->getMockbuilder('Webforge\Common\CommandOutput')->getMockForAbstractClass();
    
    $this->interaction = $this->getMockBuilder('Webforge\Console\InteractionHelper')->disableOriginalConstructor()->getMock();
    $this->installer = new PartsInstaller(array(), $this->container, $this->interaction, $this->output);
  }
  
  public function findCopyCmds(Macro $macro) {
    $copies = array();
    foreach ($macro->getCommands() as $command) {
      if ($this->isCmd('Copy', $command)) {
        $copies[] = $command;
      }
    }
    return $copies;
  }

  public function findWriteOrWriteTemplateCmds(Macro $macro) {
    $commands = array();
    foreach ($macro->getCommands() as $command) {
      if ($this->isCmd('Write', $command) || $this->isCmd('WriteTemplate', $command)) {
        $commands[] = $command;
      }
    }
    return $commands;
  }
  
  public function getCopiedFiles($macro) {
    $copiedFiles = array();
    foreach ($this->findCopyCmds($macro) as $copy) {
      $copiedFiles[(string) $copy->getSource()] = $copy->getDestination()->getUrl($this->target);
      
      $this->assertFileExists($copy->getSource(), 'source does not exist: '. $copy->describe());
    }
    
    return $copiedFiles;
  }

  public function getWrittenFiles($macro) {
    $files = array();
    foreach ($macro->getCommands() as $write) {
      if ($this->isCmd('WriteTemplate', $write)) {
        $files[(string) $write->getTemplate()] = $write->getDestination()->getUrl($this->target);
        $this->assertFileExists($write->getTemplate(), 'template does not exist: '. $write->describe());

      } elseif($this->isCmd('Write', $write)) {
        $files[] = $write->getDestination()->getUrl($this->target);
      }
    }
    
    return $files;
  }

  public function getAddedCLICommands($macro) {
    $commands = array();
    foreach ($macro->getCommands() as $addCLICommand) {
      if ($this->isCmd('AddCLI', $addCLICommand)) {
        $commands[$addCLICommand->getCLICommand()->getFQN()] = $addCLICommand->getCLICommand();
      }
    }
    
    return $commands;
  }

  public function assertHasAddedCLICommand($macro, $fqn) {
    $commands = $this->getAddedCLICommands($macro);
    $this->assertArrayHasKey($fqn, $commands, $fqn.' was not added as CLI Command. Added were ('.count($commands).'): '.implode(', ', array_keys($commands)));
  }

  
  public function isCmd($name, Command $command) {
    $class = 'Webforge\Setup\Installer\\'.$name.'Cmd';
    return $command instanceof $class;
  }
  
  public function debugMacro(Macro $macro) {
    $x = 0;
    $str = '';
    foreach ($macro->getCommands() as $command) {
      $str .= sprintf("[%s] %s\n", $x, $command->describe());
      $x++;
    }
    
    return $str;
  }

  public function expectQuestion($constraint, $answer, $type = 'all') {
    $this->interaction
     ->expects($this->once())
     ->method($type === 'all' 
       ? $this->logicalOr($this->equalTo('ask'), $this->equalTo('askAndValidate'), $this->equalTo('askDefault'), $this->equalTo('confirm')) 
       : $type
     )
     ->with($constraint)
     ->will($this->returnValue($answer));
  }

  protected function getVirtualDirectoryFromPhysical($name, Dir $physicalDirectory, $maxFileSize = 2048) {
    

    vfsStream::copyFromFileSystem((string) $physicalDirectory, $dir, $maxFileSize);

  }
}
