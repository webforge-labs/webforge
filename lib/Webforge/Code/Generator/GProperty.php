<?php

namespace Webforge\Code\Generator;

use Psc\Data\Type\Type;
use Psc\Data\Type\MixedType;

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
   * The Default Value of the property
   *
   * as it is constrained that one GProperty has only one class,
   * the default Value can be stored here (unlike Reflection from PHP)
   * @var mixed
   */
  protected $defaultValue;
  
  /**
   *
   * if you don't know the type you it will be set to Psc\Data\Type\Mixed aka unknown type
   * @param bitmap $modifiers
   */
  public function __construct($name, Type $type = NULL, $defaultValue = self::UNDEFINED, $modifiers = self::MODIFIER_PROTECTED) {
    $this->setModifiers($modifiers);
    $this->setType($type);
    $this->defaultValue = $defaultValue;
  }
  
  /**
   * @return Psc\Data\Type\Type
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
   * Setzt die Default Value des Properties in der Klasse
   *
   * muss deshalb einer zugeordnet Klasse sein ($this->getGClass())
   * @param mixed $default
   */
  public function setDefaultValue($default) {
    $this->defaultValue = $default;
    return $this;
  }
  
  /**
   * Entfernt die Default Value aus der Klasse
   *
   * dies ist nicht dasselbe wie $this->setDefaultValue(NULL) !
   */
  public function removeDefaultValue() {
    $this->defaultValue = self::UNDEFINED;
    return $this;
  }
}
?>