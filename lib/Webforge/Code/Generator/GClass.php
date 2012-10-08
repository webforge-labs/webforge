<?php

namespace Webforge\Code\Generator;

use Psc\String AS S;

class GClass extends \Psc\Code\Generate\GClass {
  
  /**
   * The personal imports of the GClass
   * 
   * @var Webforge\Code\Generator\Imports
   */
  protected $ownImports;
  
  public function __construct($class = NULL)  {
    $this->ownImports = new Imports();
    
    if ($class instanceof ReflectionClass) {
      $this->elevate($class);
    } elseif ($class instanceof GClass) {
      $this->setFQN($class->getFQN());
    } elseif (is_string($class)) {
      $this->setFQN($class);
    }
  }
  
  public static function create($fqn, $parentClass = NULL) {
    $gClass = new static($fqn);
    
    if (isset($parentClass)) {
      if (is_string($parentClass)) {
        $parentClass = self::create($parentClass);
      }
        
      $gClass->setParent($parentClass);
    }
    
    return $gClass;
  }
  
  public function setParent(GClass $parent) {
    $this->parentClass = $parent;
    return $this;
  }
  
  /**
   * returns the Name of the Class
   *
   * its the Name of the FQN without the Namespace
   * @return string
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * Sets the Name of the Class
   *
   * this is not the FQN, its only the FQN without the namespace
   * @chainable
   */
  public function setName($name) {
    $this->name = trim($name, '\\');
    return $this;
  }
  
  /**
   * @chainable
   */
  public function setNamespace($ns) {
    $this->namespace = ltrim(S::expand($ns, '\\', S::END), '\\');
    return $this;
  }

  /**
   * Returns the Namespace
   *
   * @return string The namespace has no \ before and after
   */
  public function getNamespace() {
    // i think its faster to compute the FQN with concatenation add the trailingslash in the setter and remove the trailslash here
    return isset($this->namespace) ? rtrim($this->namespace, '\\') : NULL;
  }

  /**
   * Returns the Fully Qualified Name of the Class
   *
   * this is the whole path including Namespace without a backslash before
   * @return string
   */
  public function getFQN() {
    return $this->namespace.$this->name;
  }

  public function setFQN($fqn) {
    if (FALSE !== ($pos = mb_strrpos($fqn,'\\'))) {
      $this->namespace = ltrim(mb_substr($fqn, 0, $pos+1), '\\'); // +1 to add the trailing slash, see setNamespace
      $this->setName(mb_substr($fqn, $pos));
    } else {
      $this->namespace = NULL;
      $this->setName($fqn);
    }
  }
  
  /**
   * Adds an import to the Class
   *
   * in case the class is written with a ClassWriter, these classes will be added to the file as a "use" statement
   * @throws Exception when the alias (implicit or explicit) is already used (see Imports::add())
   * @param string $alias if not given the name of the class is used
   */
  public function addImport(GClass $gClass, $alias = NULL) {
    $this->ownImports->add($gClass, $alias);
    return $this;
  }
  
  /**
   * @return bool
   */
  public function hasImport($aliasOrGClass) {
    return $this->ownImports->have($aliasOrGClass);
  }

  /**
   * Removes an Import from the Class
   *
   * @param string $alias case insensitive
   */
  public function removeImport($alias) {
    $this->ownImports->remove($alias);
    return $this;
  }
  
  
  /**
   * Returns the Imports which are used in the code of the class
   *
   * These are all needed exports to make the code compile
   * 
   * @return Webforge\Code\Generate\Imports
   */
  public function getImports() {
    // @TODO extract from properties, methods, interfaces
    
    return $this->ownImports;
  }
  
  /**
   * @return string
   */
  public function __toString() {
    return $this->getFQN();
  }
  
  public function getClassName() {
    //throw new \Psc\DeprecatedException('getClassName() is deprecated. Use getName instead');
    return $this->name;
  }
}
?>