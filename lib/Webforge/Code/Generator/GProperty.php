<?php

namespace Webforge\Code\Generator;

use Webforge\Types\Type;
use Webforge\Types\MixedType;
use Webforge\Types\ObjectType;

/**
 * a GProperty models a property of a GClass
 *
 * it has a name
 * a type
 * a default Value
 *
 * it's declared by one class (only one) and this class is optional
 */
class GProperty extends GModifiersObject {
  
  /**
   * The name of the property
   * 
   * @var string 
   */
  protected $name;
  
  /**
   * The default value of the property
   *
   * as it is constrained that one GProperty has only one class,
   * the default Value can be stored here (unlike Reflection from PHP)
   * @var mixed
   */
  protected $defaultValue;
  
  /**
   * The type of the property
   * 
   * @var Webforge\Types\Type
   */
  protected $type;

  /**
   * @var Webforge\Code\Generator\GClass
   */
  protected $gClass;
  
  /**
   *
   * if you don't know the type you it will be set to Webforge\Types\Mixed aka unknown type
   * @param bitmap $modifiers
   */
  public function __construct($name, Type $type = NULL, $defaultValue = self::UNDEFINED, $modifiers = self::MODIFIER_PROTECTED) {
    $this->setName($name);
    $this->setType($type);
    $this->setModifiers($modifiers);
    $this->defaultValue = $defaultValue;
  }

  /**
   * Creates a new GProperty
   *
   * for your convenience, you can use GClass as $type. It will then be converted to the Object<GClass->getFQN()>-Type
   */
  public static function create($name, $type = NULL, $defaultValue = self::UNDEFINED, $modifiers = self::MODIFIER_PROTECTED) {
    if (isset($type)) {
      if ($type instanceof GClass) {
        $type = new ObjectType(new \Psc\Code\Generate\GClass($type->getFQN())); // bc compability, unfortunately
      } elseif (!($type instanceof Type)) {
        $type = Type::create($type);
      }
    }
    
    return new static($name, $type, $defaultValue, $modifiers);
  }
  
  /**
   * Returns (always) the type of the Property
   *
   * the type might be implicit (aka: MixedType)
   * @return Webforge\Types\Type
   */
  public function getType() {
    return $this->type;
  }
  
  /**
   * Sets the Type of the property
   *
   * @param Type|NULL $type if type is NULL it is set to Mixed
   * @chainable
   */
  public function setType(Type $type = NULL) {
    $this->type = $type ?: new MixedType();
    return $this;
  }
  
  /**
   * @return bool
   */
  public function hasExplicitType() {
    return !($this->type instanceof MixedType);
  }

  /**
   * @return mixed
   */
  public function getDefaultValue() {
    return $this->defaultValue;
  }
  
  /**
   * @return bool
   */
  public function hasDefaultValue() {
    return $this->defaultValue !== self::UNDEFINED;
  }
  
  /**
   * Sets the defaultValue for the Property
   *
   * @param mixed|self::UNDEFINED $default if value is === self::UNDEFINED then the default value is removed
   */
  public function setDefaultValue($default) {
    $this->defaultValue = $default;
    return $this;
  }
  
  /**
   * Removes the DefaultValue for the Property
   *
   * remember: this is not aquivalent to: $this->setDefaultValue(NULL)
   */
  public function removeDefaultValue() {
    $this->defaultValue = self::UNDEFINED;
    return $this;
  }
  
  // @codeCoverageIgnoreStart
  /**
   * @param string $name
   * @chainable
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
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
   * @param Webforge\Code\Generator\GClass $gClass
   * @chainable
   */
  public function setGClass(GClass $gClass) {
    $this->gClass = $gClass;
    return $this;
  }

  /**
   * @return Webforge\Code\Generator\GClass
   */
  public function getGClass() {
    return $this->gClass;
  }
  // @codeCoverageIgnoreEnd
}
