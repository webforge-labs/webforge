<?php

namespace Webforge\Code\Test;

use Psc\Code\Code;
use Psc\System\Dir;

/**
 * Changes to the PHPUnit-API:
 *
 * - allow just a className (without namespace) for getMock and getMockForAbstractClass. It uses the current Test-Namespace
 * - allow Psc\System\File for file-related
 * - add assertArrayEquals() as a short coming for equals() with $canonicalize = true
 */
class Base extends \Psc\Code\Test\Base {

  /**
   * @var Psc\System\Dir
   */
  protected $testFilesDirectory;
    
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
  
  /**
   * @return Psc\System\Dir
   */
  public function getTestDirectory($sub = '/') {
    if (!isset($this->testFilesDirectory)) {
      // @TODO change hardcoding
      $this->testFilesDirectory = new Dir(
                                  __DIR__.DIRECTORY_SEPARATOR.
                                  '..'.DIRECTORY_SEPARATOR. //  Code
                                  '..'.DIRECTORY_SEPARATOR. //  Webforge
                                  '..'.DIRECTORY_SEPARATOR. //  lib
                                  '..'.DIRECTORY_SEPARATOR. //  root
                                  'tests'.DIRECTORY_SEPARATOR.
                                  'files'.DIRECTORY_SEPARATOR
                                );
    }
    
    return $this->testFilesDirectory->sub($sub);
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