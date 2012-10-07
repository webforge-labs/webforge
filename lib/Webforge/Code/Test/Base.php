<?php

namespace Webforge\Code\Test;

use Psc\Code\Code;

/**
 * Changes to the PHPUnit-API:
 *
 * - allow just a className (without namespace) for getMock and getMockForAbstractClass. It uses the current Test-Namespace
 * - allow Psc\System\File for file-related
 * - add assertArrayEquals() as a short coming for equals() with $canonicalize = true
 */
class Base extends \Psc\Code\Test\Base {
    
  /**
   * Asserts that actualCode is equivalent to expectedCode (as PHP Code)
   *
   * Code is considered equal, when it is equal without comments and normalized whitespace
   *
   * @param string $expectedCode complete PHP Code
   * @param string $actualCode complete PHP Code
   */
  public static function assertCodeEquals($actualCode, $expectedCode, $message = '') {
    self::assertThat($expectedCode, self::codeEqualTo($actualCode), $message);
  }
  
  public static function codeEqualTo($code) {
    return new CodeEqualsConstraint($code);
  }
  
  
  /* PHPUnit extensions */
  
  public static function assertFileExists($filename, $message = '') {
    if ($filename instanceof \Psc\System\File) {
      $filename = (string) $filename;
    }
    return parent::assertFileExists($filename, $message);
  }

  
  public function getMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE) {
    $originalClassName = Code::expandNamespace($originalClassName, Code::getNamespace(get_class($this)));
    
    return parent::getMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
  }
  
  public function getMockForAbstractClass($originalClassName, array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE, $mockedMethods = array()) {
    $originalClassName = Code::expandNamespace($originalClassName, Code::getNamespace(get_class($this)));
    return parent::getMockForAbstractClass($originalClassName, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $mockedMethods);
  }
  
}
?>