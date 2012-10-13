<?php

namespace Webforge\Code\Generator;

use Psc\String AS S;
use ReflectionClass;

class GClass extends GModifiersObject {
  
  const WITHOUT_CONSTRUCTOR = TRUE;
  const END = GObjectCollection::END;

  /**
   * @var string
   */
  protected $name;

  /**
   * @var string
   */
  protected $namespace;

  /**
   * @var Webforge\Code\Generator\GClass
   */
  protected $parentClass;

  /**
   * @var GObjectCollection(Webforge\Code\Generator\GClass)
   */
  protected $interfaces;

  /**
   * @var GObjectCollection(Webforge\Code\Generator\GProperty)
   */
  protected $properties;

    /**
   * @var GObjectCollection(Webforge\Code\Generator\GMethod)
   */
  protected $methods;

  /**
   * @var GObjectCollection(Webforge\Code\Generator\GConstant)
   */
  protected $constants;
  
  /**
   * The personal imports of the GClass
   * 
   * @var Webforge\Code\Generator\Imports
   */
  protected $ownImports;
  
  
  public function __construct($class = NULL)  {
    $this->ownImports = new Imports();
    $this->interfaces = new GObjectCollection(array());
    $this->methods = new GObjectCollection(array());
    $this->properties = new GObjectCollection(array());
    $this->constants = new GObjectCollection(array());
    
    if ($class instanceof GClass) {
      $this->setFQN($class->getFQN());
    } elseif (is_string($class)) {
      $this->setFQN($class);
    }
  }
  
  /**
   * @return Webforge\Code\Generator\GClass
   */
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

  /**
   * @return Object<{$this->getFQN()}>
   */
  public static function newClassInstance($class, Array $params = array()) {
    if ($class instanceof GClass) {
      return $class->newInstance($params);
      
    } elseif ($class instanceof ReflectionClass) {
      $refl = $class;

    } elseif (is_string($class)) {
      $refl = new ReflectionClass($class);
      
    } else {
      throw new InvalidArgumentException('class kann nur string,gclass oder reflectionclass sein');
    }
    
    return $refl->newInstanceArgs($params);
  }
  
  /**
   * @return Object<{$this->getFQN()}>
   */
  public function newInstance(Array $params = array(), $dontCallConstructor = FALSE) {
    
    if ($dontCallConstructor) {
      // Creates a new instance of the mapped class, without invoking the constructor.
      if (!isset($this->prototype)) {
        $this->prototype = unserialize(sprintf('O:%d:"%s":0:{}', mb_strlen($this->getFQN()), $this->getFQN()));
      }
      
      return clone $this->prototype;
    }
    
    return $this->getReflection()->newInstanceArgs($params);
  }
  
  /**
   * @return ReflectionClass
   */
  public function getReflection() {
    return new ReflectionClass($this->getFQN());
  }

  ///**
  // * Creates a new Property and dass it to the class
  // * 
  // * @return GProperty
  // */
  //public function createProperty($name, $modifiers = GProperty::MODIFIER_PROTECTED, $default = 'undefined') {
  //  $gProperty = new GProperty($this);
  //  $gProperty->setName($name);
  //  $gProperty->setModifiers($modifiers);
  //  if (func_num_args() == 3) {
  //    $this->setDefaultValue($gProperty,$default);
  //  }
  //  $this->addProperty($gProperty);
  //  return $gProperty;
  //}
  //
  
  /**
   * Creates a new Method and adds it to the class
   *
   * notice: this returns a gMethod and is not chainable
   * but you can "leave" the chain with getGClass()
   * @return GMethod
   */
  public function createMethod($name, $params = array(), $body = NULL, $modifiers = GMethod::MODIFIER_PUBLIC) {
    $method = new GMethod($name, $params, $body, $modifiers);
    $method->setGClass($this);
    
    $this->addMethod($method);
    
    return $method;
  }

  /**
   * Erstellt Stubs (Prototypen) für alle abstrakten Methoden der Klasse
   */             
  public function createAbstractMethodStubs() {
    if ($this->isAbstract()) return $this;
    
    if (($parent = $this->getParent()) !== NULL) {
      //$parent->elevateClass();
      foreach ($parent->getAllMethods() as $method) {
        if ($method->isAbstract()) {
          $this->createMethodStub($method);
        }
      }
    }
    
    foreach ($this->getInterfaces() as $interface) {
      //$interface->elevateClass();
      foreach ($interface->getAllMethods() as $method) {
        $this->createMethodStub($method);
      }
    }
    return $this;
  }
  
  /**
   * Erstellt einen Stub für eine gegebene abstrakte Methode
   */
  public function createMethodStub(GMethod $method) {
    // method is not abstract (thats strange)
    if (!$method->isAbstract()) return $this;
    
    // no need to implement
    if ($this->hasMethod($method->getName()) && !$method->isAbstract()) return $this;
    
    $cMethod = clone $method;
    $cMethod->setAbstract(FALSE);
    $cMethod->setGClass($this);
    return $this->addMethod($cMethod);
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
  
  public function getKey() {
    return $this->getFQN();
  }
  
  /**
   * Replaces the Namespace and Name of the Class
   *
   * @param string $fqn no \ before
   */
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
   * @chainable
   */
  public function addInterface(GClass $interface, $position = self::END) {
    $this->interfaces->add($interface, $position);
    return $this;
  }
  
  /**
   * @return GClass
   */
  public function getInterface($fqnOrIndex) {
    return $this->interfaces->get($fqnorIndex);
  }
  
  /**
   * @return bool
   */
  public function hasInterface($fqnOrClass) {
    return $this->interfaces->has($fqnOrClass);
  }
  
  /**
   * @chainable
   */
  public function removeInterface($fqnOrClass) {
    $this->interfaces->remove($fqnOrClass);
    return $this;
  }
  
  /**
   * @chainable
   */
  public function setInterfaceOrder(GClass $interface, $position) {
    $this->interfaces->setOrder($interface, $position);
    return $this;
  }
  
  /**
   * @return array
   */
  public function getInterfaces() {
    return $this->interfaces->toArray();
  }

  
  /**
   * @param array
   */
  public function setInterfaces(Array $interfaces) {
    $this->interfaces = new GObjectCollection($interfaces);
    return $this;
  }

  /**
   * @chainable
   */
  public function addConstant(GConstant $interface, $position = self::END) {
    $this->constants->add($interface, $position);
    return $this;
  }
  
  /**
   * @return GClass
   */
  public function getConstant($fqnOrIndex) {
    return $this->constants->get($fqnorIndex);
  }
  
  /**
   * @return bool
   */
  public function hasConstant($fqnOrClass) {
    return $this->constants->has($fqnOrClass);
  }
  
  /**
   * @chainable
   */
  public function removeConstant($fqnOrClass) {
    $this->constants->remove($fqnOrClass);
    return $this;
  }
  
  /**
   * @chainable
   */
  public function setConstantOrder(GConstant $interface, $position) {
    $this->constants->setOrder($interface, $position);
    return $this;
  }
  
  /**
   * @return array
   */
  public function getConstants() {
    return $this->constants->toArray();
  }

  /**
   * @param array
   */
  public function setConstants(Array $constants) {
    $this->constants = new GObjectCollection($constants);
    return $this;
  }

  /**
   * @chainable
   */
  public function addProperty(GProperty $interface, $position = self::END) {
    $this->properties->add($interface, $position);
    return $this;
  }
  
  /**
   * @return GClass
   */
  public function getProperty($fqnOrIndex) {
    return $this->properties->get($fqnorIndex);
  }
  
  /**
   * @return bool
   */
  public function hasProperty($fqnOrClass) {
    return $this->properties->has($fqnOrClass);
  }
  
  /**
   * @chainable
   */
  public function removeProperty($fqnOrClass) {
    $this->properties->remove($fqnOrClass);
    return $this;
  }
  
  /**
   * @chainable
   */
  public function setPropertyOrder(GProperty $interface, $position) {
    $this->properties->setOrder($interface, $position);
    return $this;
  }
  
  /**
   * @return array
   */
  public function getProperties() {
    return $this->properties->toArray();
  }

  /**
   * @return array
   */
  public function setProperties(Array $properties) {
    $this->properties = new GObjectCollection($properties);
    return $this;
  }

    /**
   * @chainable
   */
  public function addMethod(GMethod $interface, $position = self::END) {
    $this->methods->add($interface, $position);
    return $this;
  }
  
  /**
   * @return GClass
   */
  public function getMethod($fqnOrIndex) {
    return $this->methods->get($fqnOrIndex);
  }
  
  /**
   * @return bool
   */
  public function hasMethod($fqnOrClass) {
    return $this->methods->has($fqnOrClass);
  }
  
  /**
   * @chainable
   */
  public function removeMethod($fqnOrClass) {
    $this->methods->remove($fqnOrClass);
    return $this;
  }
  
  /**
   * @chainable
   */
  public function setMethodOrder(GMethod $interface, $position) {
    $this->methods->setOrder($interface, $position);
    return $this;
  }
  
  /**
   * Returns the (own) methods of the class
   * @return array
   */
  public function getMethods() {
    return $this->methods->toArray();
  }
  
  /**
   * Returns the methods of the class and the methods of all parents
   *
   */
  public function getAllMethods() {
    $methods = clone $this->methods;
    
    if ($this->getParent() != NULL) {
      // treat duplicates (aka: overriden methods):
      foreach ($this->getParent()->getAllMethods() as $method) {
        if (!$methods->has($method)) {
          $methods->add($method);
        }
      }
    }
    
    return $methods->toArray();
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

  /**
   * @param Webforge\Code\Generator\GClass $parent
   */
  public function setParent(GClass $parent) {
    $this->parentClass = $parent;
    return $this;
  }
  
  /**
   * @return Webforge\Code\Generator\GClass
   */
  public function getParent() {
    return $this->parentClass;
  }
  
  /**
   * @return bool
   */
  public function equals(GClass $otherClass) {
    return $this->getFQN() === $otherClass->getFQN();
  }
  
  /**
   * @return bool
   */
  public function exists($autoload = TRUE) {
    return class_exists($this->getFQN(), $autoload);
  }
}
?>