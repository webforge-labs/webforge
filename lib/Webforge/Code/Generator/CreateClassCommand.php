<?php

namespace Webforge\Code\Generator;

use Webforge\Framework\Container;
use Closure;

/**
 * Easier usage of the classCreater
 */
class CreateClassCommand {
  
  /**
   * @var Webforge\Code\Generator\ClassCreater
   */
  protected $classCreater;
  
  /**
   * is avaible after fqn()
   * @var Webforge\Code\Generator\GClass
   */
  protected $gClass;
  
  /**
   * is avaible after write()
   * @var Psc\System\File
   */
  protected $file;
  
  public function __construct(ClassCreater $classCreater) {
    $this->classCreater = $classCreater;
  }
  
  /**
   * @return Webforge\Code\Generator\CreateClassCommand
   */
  public static function fromContainer(Container $container) {
    return new static(
      new ClassCreater(
        $container->getClassFileMapper(),
        $container->getClassWriter(),
        $container->getClassElevator()
      )
    );
  }
  
  public function reset() {
    $this->file = NULL;
    $this->gClass = NULL;
    return $this;
  }
  
  /**
   * @chainable
   */
  public function fqn($fqn) {
    $this->reset();
    $this->gClass = new GClass($fqn);
    return $this;
  }
  
  /**
   * @chainable
   */
  public function parent($fqn) {
    $this->gClass->setParent(new GClass($fqn));
    return $this;
  }
  
  /**
   * @chainable
   */
  public function addInterface($fqn) {
    $this->gClass->addInterface(new GInterface($fqn));
    return $this;
  }
  
  /**
   *
   * @param Closure $do function(GClass $gClass)
   * @chainable
   */
  public function withGClass(Closure $do) {
    $do($this->gClass);
    return $this;
  }
  
  /**
   * @return Webforge\Code\Generator\GClass
   */
  public function getGClass() {
    return $this->gClass;
  }
  
  /**
   * @return Psc\System\File
   */
  public function getFile() {
    return $this->file;
  }

  /**
   * @chainable
   */
  public function write($overwrite = FALSE) {
    $this->file = $this->classCreater->create($this->gClass, $overwrite ? ClassCreater::OVERWRITE : FALSE);
    
    return $this;
  }
  
  /**
   * @chainable
   */
  public function overwrite() {
    return $this->write(TRUE);
  }
}
?>