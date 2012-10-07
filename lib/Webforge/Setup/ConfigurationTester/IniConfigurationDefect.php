<?php

namespace Webforge\Setup\ConfigurationTester;

/**
 * 
 */
class IniConfigurationDefect extends ConfigurationDefect {
  
  /**
   * @var string
   */
  protected $name;
  
  /**
   * @var mixed
   */
  protected $expectedValue;
  protected $normalizedExpectedValue;
  
  /**
   * @var mixed
   */
  protected $actualValue;
  protected $normalizedActualValue;
  
  /**
   * @var string
   */
  protected $operator;
  
  public function __construct($name, $expectedValue, $normalizedExpected, $actualValue, $normalizedActual, $operator) {
    $this->setName($name);
    $this->setExpectedValue($expectedValue);
    $this->normalizedExpectedValue = $normalizedExpected;
    $this->setActualValue($actualValue);
    $this->normalizedActualValue = $normalizedActual;
    $this->setOperator($operator);
  }
  
  public function __toString() {
    return sprintf('[ini]: failed that %s expected: %s is %s actual: %s (normalized: %s %3$s %s)',
                   $this->name,
                   $this->valueToString($this->expectedValue),
                   $this->operator,
                   $this->valueToString($this->actualValue),
                   $this->valueToString($this->normalizedExpectedValue),
                   $this->valueToString($this->normalizedActualValue)
                  );
    //return sprintf('[ini]: failed that %s expected: %s is %s actual: %s', $this->name, $this->expectedValue, $this->operator, $this->actualValue);
  }
  
  protected function valueToString($value) {
    if (is_bool($value)) {
      return $value ? '(boolean) true' : '(boolean) false';
    }
    return (string) $value;
  }
  
  /**
   * @param string $name
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
   * @param mixed $expectedValue
   */
  public function setExpectedValue($expectedValue) {
    $this->expectedValue = $expectedValue;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getExpectedValue() {
    return $this->expectedValue;
  }
  
  /**
   * @param mixed $actualValue
   */
  public function setActualValue($actualValue) {
    $this->actualValue = $actualValue;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getActualValue() {
    return $this->actualValue;
  }
  
  /**
   * @param string $operator
   */
  public function setOperator($operator) {
    $this->operator = $operator;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getOperator() {
    return $this->operator;
  }
}
?>