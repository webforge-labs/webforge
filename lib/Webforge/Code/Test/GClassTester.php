<?php

namespace Webforge\Code\Test;

use Webforge\Code\Generator\GClass;
use Webforge\Code\Generator\GProperty;
use Webforge\Code\Generator\GMethod;
use Webforge\Code\Generator\GModifiersObject;
use Webforge\Common\Exception\NotImplementedException;

class GClassTester {
  
  protected $test;
  
  protected $gClass;
  
  public function __construct(GClass $gClass, \Webforge\Code\Test\Base $testCase) {
    $this->test = $testCase;
    $this->gClass = $gClass;
  }
  
  public function hasMethod($name, array $parameters = NULL) {
    $this->test->assertTrue($this->gClass->hasMethod($name),$this->msg("hasMethod '%s'", $name));
    $method = $this->gClass->getMethod($name);    
    
    if (isset($parameters)) {
      $this->assertMethodParameters($method, $parameters);
    }
    
    $this->lastGet = $method;
    
    return $this;
  }
  
  public function getMethod($name) {
    return $this->gClass->getMethod($name);  
  }
  
  /**
   * @param array $parameters wenn ein String wird nur der Name des Parameters gecheckt
   */
  public function assertMethodParameters(GMethod $m, Array $parameters) {
    $this->lastGet = $methodParameters = $m->getParameters();
    $debugParameters = array_map(function ($p) {
        return $p->getName();
      },
      $m->getParameters()
    );
    
    foreach ($parameters as $key => $parameter) {
      if (is_string($parameter)) {
        $this->test->assertArrayHasKey($key, $methodParameters,
                                       $this->msg("MethodParameter %d existiert nicht in '%s'. Parameter sind: [%s]", $key, $m->getName(), implode(", ",$debugParameters)));
        
        $this->test->assertEquals($methodParameters[$key]->getName(), $parameter,
                                  $this->msg("MethodParameter %d hat nicht den Namen %s in '%s'. Parameter sind: [%s]", $key, $parameter, $m->getName(), implode(", ",$debugParameters)));
        
      } else {
        throw NotImplementedException::fromString('Parameter können bis jetzt nur strings sein');
      }
    }
    
    return $this;
  }

  public function hasProperty($name) {
    $this->test->assertTrue(
      $this->gClass->hasProperty($name),
      $this->msg(
        "hasProperty '%s'. Properties avaible are:\n%s",
        $name, $this->debugProperties()
      )
    );
    
    $this->lastGet = $this->gClass->getProperty($name);
    
    return $this;
  }

  public function hasNotProperty($name) {
    $this->test->assertFalse($this->gClass->hasProperty($name),$this->msg("hasNotProperty '%s'", $name));
    return $this;
  }

  public function hasNotOwnProperty($name) {
    $this->test->assertFalse(
                             $this->gClass->hasOwnProperty($name),$this->msg("hasNotOwnProperty '%s'", $name)
                             );
    return $this;
  }

  public function hasConstant($name) {
    $this->test->assertTrue(
      $this->gClass->hasConstant($name),
      $this->msg(
        "hasConstant '%s'. Constants avaible are:\n%s",
        $name, $this->debugConstants()
      )
    );
    
    $this->lastGet = $this->gClass->getConstant($name);
    
    return $this;
  }
  
  /**
   * @return string
   */
  protected function debugConstants() {
    return json_encode(
      array_map(
        function ($prop) {
          return $prop->getName();
        },
        $this->gClass->getConstants()
      )
    );
  }
  
  /**
   * @return string
   */
  protected function debugProperties() {
    return json_encode(
      array_map(
        function ($prop) {
          return $prop->getName();
        },
        $this->gClass->getProperties()
      )
    );
  }

  public function hasOwnProperty($name) {
    $this->test->assertTrue(
                             $this->gClass->hasOwnProperty($name),$this->msg("hasOwnProperty '%s'", $name)
                             );
    return $this;
  }

  public function hasInterface($gClass) {
    if (!($gClass instanceof GClass)) {
      $gClass = new GClass($gClass);
    }
    
    $this->test->assertTrue($this->gClass->hasInterface($gClass),$this->msg("hasInterface '%s'", $gClass->getFQN()));
    return $this;
  }

  public function hasNotInterface($gClass) {
    if (!($gClass instanceof GClass)) {
      $gClass = new GClass($gClass);
    }
    
    $this->test->assertFalse($this->gClass->hasInterface($gClass),$this->msg("hasNotInterface '%s'", $gClass->getFQN()));
    return $this;
  }
  
  public function isStatic() {
    return $this->hasModifier(GModifiersObject::MODIFIER_STATIC, 'static');
  }

  public function isFinal() {
    return $this->hasModifier(GModifiersObject::MODIFIER_FINAL, 'final');
  }

  public function isAbstract() {
    return $this->hasModifier(GModifiersObject::MODIFIER_ABSTRACT, 'abstract');
  }

  public function isPublic() {
    return $this->hasModifier(GModifiersObject::MODIFIER_PUBLIC, 'public');
  }

  public function isProtected() {
    return $this->hasModifier(GModifiersObject::MODIFIER_PROTECTED, 'protected');
  }

  public function isPrivate() {
    return $this->hasModifier(GModifiersObject::MODIFIER_PRIVATE, 'private');
  }
    
  public function hasModifier($modifier, $modifierName) {
    if ($this->lastGet instanceof GMethod) {
      $this->test->assertTrue(
        (bool) ($this->lastGet->getModifiers() & $modifier),
        $this->msg("method '%s' is %s", $this->lastGet->getName(), $modifierName)
      );
    } elseif ($this->lastGet instanceof GProperty) {
      $this->test->assertTrue(
        (bool) ($this->lastGet->getModifiers() & $modifier),
        $this->msg("property '%s' is %s", $this->lastGet->getName(), $modifierName)
      );
    } else {
      $this->test->assertTrue(
        (bool) ($this->gClass->getModifiers() & $modifier),
        $this->msg("is %s", $modifierName)
      );
    }
    return $this;
  }

  /**
   * Asserts the type of a property/constant
   */
  public function isType($typeFQN) {
    if ($this->lastGet instanceof GProperty) {
      $this->test->assertInstanceOf(
        $typeFQN,
        $this->lastGet->getType(),
        $this->msg("property '%s' is of type %s", $this->lastGet->getName(), $typeFQN)
      );
    } else {
      throw new \RuntimeException('lastGet is not a Property/Constant. Other GObjects do not have a type');
    }
    
    return $this;
  }
  
  /**
   * Gibt das Objekt der letzten assertion zurück
   *
   * hasMethod -> gMethod
   * hasProperty -> gProperty
   * assertMethodParameters -> Parameters[]
   * 
   * @return mixed
   */
  public function get() {
    return $this->lastGet;
  }
  
  /**
   * @param string msg
   * @param string $sprintfParam, ...
   */
  protected function msg($msg) {
    $args = func_get_args();
    array_shift($args);
    $msg = '[GClassTester: '.$this->gClass->getFQN().'] '.$msg;
    
    return vsprintf($msg, $args);
  }
}
?>