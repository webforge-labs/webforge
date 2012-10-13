<?php

namespace Webforge\Code\Generator;

use Psc\Data\Type\Type;
use Psc\Data\Type\MixedType;
use Psc\Data\Type\ObjectType;

/**
 * Models a GParameter for a GFunction / GMethod
 *
 * GParameter must have a specific type (see GProperty)
 */
class GParameter extends GObject {
  
  /**
   * The name of the parameter
   * 
   * @var string
   */
  protected $name;
  
  /**
   * Is the Parameter passed as reference
   * @var bool
   */
  protected $reference;
  
  /**
   * The type of the parameter
   *
   * @var Psc\Data\Type\Type
   */
  protected $type;
  
  /**
   * Is the parameter hint an array?
   * 
   * @var bool
   */
  protected $array;
  
  /**
   * The Default value of the Paramter
   * @var mixed
   */
  protected $default = self::UNDEFINED;
  
  /**
   * Creates a Parameter for a Method or a Function
   * 
   * if you don't know the type you it will be set to Psc\Data\Type\Mixed aka unknown type
   * @param Psc\Data\Type $type
   */
  public function __construct($name, Type $type = NULL, $default = self::UNDEFINED, $reference = FALSE) {
    $this->name = $name;
    $this->setType($type);
    $this->default = $default;
    $this->reference = (bool) $reference;
  }
  
  /**
   * Creates a new Parameter
   *
   * for your convenience, you can use GClass as $type. It will then be converted to the Object<GClass->getFQN()>-Type
   * You can also use "array" or other shortcomings for type creation
   * @param string|GClass|Type $type 
   * @return Webforge\Code\Generator\GParameter
   */
  public static function create($name, $type = NULL, $defaultValue = self::UNDEFINED, $reference = FALSE) {
    if (isset($type)) {
      if ($type instanceof GClass) {
        $type = new ObjectType(new \Psc\Code\Generate\GClass($type->getFQN())); // unfortunately for backward compability
      } elseif (!($type instanceof Type)) {
        $type = Type::create($type);
      }
    }
    
    return new static($name, $type, $defaultValue, $reference);
  }

  /**
   * @param Psc\Data\Type\Type $type
   * @chainable
   */
  public function setType(Type $type = NULL) {
    if (!isset($type)) {
      $type = new MixedType();
    }
    $this->type = $type;
    return $this;
  }

  /**
   * Returns the Hint of the Parameter as a String
   *
   * @return string
   */
  public function getHint($namespaceContext = NULL) {
    return ltrim($this->type->getPHPHint($namespaceContext), '\\') ?: NULL;
  }
  
  /**
   * The Parameter is Optional if it has a default Value
   * @return bool
   */
  public function isOptional() {
    return $this->hasDefault();
  }
  
  /**
   * @return bool
   */
  public function hasDefault() {
    return $this->default !== self::UNDEFINED;
  }
  
  /**
   * @return bool
   */
  public function isReference() {
    return $this->reference;
  }

  /**
   * @return bool
   */
  public function isArray() {
    return $this->array;
  }
  
  /**
   * @chainable
   */
  public function setDefault($default) {
    $this->default = $default;
    return $this;
  }
  
  public function setName($name) {
    $this->name = ltrim($name,'$');
    return $this;
  }
  
  /**
   * @return string without $
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * @return string
   */
  public function getKey() {
    return $this->getName();
  }
  
  /**
   * @return mixed|self::UNDEFINED 
   */
  public function getDefault() {
    return $this->default;
  }

  /**
   * Removes the Default for the Parameter
   *
   */
  public function removeDefault() {
    $this->default = self::UNDEFINED;
    return $this;
  }
  
  /**
   * @return Psc\Data\Type\Type
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @return bool
   */
  public function hasExplicitType() {
    return !($this->type instanceof MixedType);
  }
}
?>