<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Webforge\Framework\ContainerAware;
use Webforge\Framework\Container;
use Webforge\Framework\Package\PackageAware;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Helper\DialogHelper;
use Webforge\Console\InteractionHelper;
use Webforge\Code\Generator\CreateClassCommand;
use Webforge\Code\Generator\GClass;
use Webforge\Common\CommandOutput as OutputInterface;

/**
 * @todo an output interface to communicate and warn
 */
class PartsInstaller implements Installer {

  /**
   * @var Parts[]
   */
  protected $parts;
  
  /**
   * @var Webforge\Framework\Container;
   */
  protected $container;
  
  /**
   * @var Webforge\Common\System\Dir
   */
  protected $target;

  /**
   * @var Webforge\Console\InteractionHelper
   */
  protected $interaction;

  /**
   * @param $container Container make sure that this container has a localPackage defined
   */
  public function __construct(Array $parts, Container $container, InteractionHelper $interaction, OutputInterface $output = NULL) {
    $this->parts = $parts;
    $this->container = $container;
    $this->output = $output ?: new NullOutput();
    $this->interaction = $interaction;
  }
  
  public function install(Part $part, Dir $destination) {
    $this->target = $destination;
    $macro = $this->installPart($part, $destination);

    return $macro->execute($this->output);
  }

  /**
   * @return Webforge\Setup\Installer\Macro
   */
  public function dryInstall(Part $part, Dir $destination) {
    $this->target = $destination;
    $macro = $this->installPart($part, $destination);
    
    return $macro;
  }
  
  protected function installPart(Part $part, Dir $destination) {
    if ($part instanceof ContainerAware) {
      $part->setContainer($this->container);
    }

    if ($part instanceof PackageAware) {
      $part->setPackage($this->container->getLocalPackage());
    }
    
    $macro = $this->recordMacro();
    $part->installTo($destination, $this);
    
    return $macro;
  }
  
  protected function recordMacro() {
    return $this->macro = new Macro(array());
  }
  
  /**
   * @param Dir|File $source
   * @param Dir|File $destination if $destination is a Dir and $source is a file, the a file with $source->getName() will be copied to $destination
   */
  public function copy($source, $destination, $flags = 0x000000) {
    
    return $this->command(
      new CopyCmd($source, $destination, $flags)
    );
  
  }

  public function execute($cmd) {
    
    return $this->command(
      new ExecCmd($cmd)
    );
    
  }
  
  public function write($contents, File $destination, $flags = 0x000000) {
    
    return $this->command(
      new WriteCmd($contents, $destination, $flags)
    );
  
  }

  public function writeTemplate(File $template, File $destination, Array $vars = array(), $flags = 0x000000) {
    
    return $this->command(
      new WriteTemplateCmd($template, $destination, $vars, $flags)
    );
  
  }
  
  /**
   * $directory can be a subdirectory of target (as a string with forward slashes)
   * @return Webforge\Common\System\Dir
   */
  public function createDir($directory) {
    // when modelled command, it should really return the directory here: 
    
    if (!($directory instanceof Dir)) {
      $directory = $this->target->sub($directory);
    }
    
    $directory->create();
    
    return $directory;
  }

  /**
   * 
   * @YAGNI: if needed relativeClassName could be given as GClass to determine the FQN (better a FQN Class?)
   * @param string $relatitveClassName will be expanded with the namespace of the local package
   * @return Webforge\Code\Generator\CreateClassCommand
   */
  public function createClass($relativeClassName, $flags = 0x000000, File $destination = NULL) {
    $createClassCmd = CreateClassCommand::fromContainer($this->container, $this->getPackage()->getNamespace())
      ->name($relativeClassName)
      ->setFileFromPackage($this->getPackage())
    ;

    $this->command(
      new CreateClassCmd($createClassCmd, $destination, $flags)
    );

    return $createClassCmd;
  }


  public function addCLICommand(GClass $command) {
    $this->command(
      new AddCLICmd($command)
    );
  }
  
  /**
   * @return Webforge\Common\System\Dir
   */
  public function getWebforgeResources() {
    return $this->container->getResourceDirectory();
  }
  
  /**
   * @return Webforge\Common\System\Dir
   */
  public function getInstallTemplates() {
    return $this->getWebforgeResources()->sub('installTemplates/');
  }

  /**
   * @return Webforge\Common\System\File
   */
  public function getInstallTemplate($relativePath) {
    return $this->getInstallTemplates()->getFile($relativePath);
  }

  protected function command(Command $cmd) {
    $this->macro->addCommand($cmd);
    return $cmd;
  }
  
  /**
   * @param string $question 
   */
  public function ask($question, $default = NULL, \Closure $validator = NULL, $attempts = FALSE) {
    if ($validator) {
      return $this->interaction->askAndValidate($question, $validator, $attempts, $default);
    } elseif($default !== NULL) {
      return $this->interaction->askDefault($question, $default);
    } else {
      return $this->interaction->ask($question, $default);
    }
  }

  public function confirm($question) {
    return $this->interaction->confirm($question);
  }
  
  public function warn($msg) {
    return $this->output->warn($msg);
  }

  public function info($msg) {
    return $this->output->msg($msg);
  }
  
  public function getPart($name) {
    $names = array();
    foreach ($this->parts as $part) {
      if ($part->getName() === $name) {
        return $part;
      }
      $names[] = $part->getName();
    }
    
    throw new \RuntimeException(
      sprintf("Part with name '%s' does not exist. Avaible parts are: %s", $name, implode(', ', $names))
    );
  }
  
  /**
   * @chainable
   */
  public function addPart(Part $part) {
    $this->parts[] = $part;
    return $this;
  }
  
  /**
   * @var array
   */
  public function getParts() {
    return $this->parts;
  }

  protected function getPackage() {
    return $this->container->getLocalPackage();
  }
}
