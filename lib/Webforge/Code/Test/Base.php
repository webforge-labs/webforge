<?php

namespace Webforge\Code\Test;

use Psc\Code\Code;
use Psc\System\Dir;
use Webforge\Code\Generator\GClass;

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
    self::assertThat($actualCode, self::codeEqualTo($expectedCode), $message);
  }
  
  public static function codeEqualTo($code) {
    return new CodeEqualsConstraint($code);
  }

  public function assertGCollectionEquals(Array $objectKeys, $collection) {
    $this->assertArrayEquals(
      $objectKeys,
      $this->reduceCollection($collection, 'key')
    );
  }
  
  /**
   * @return Webforge\Code\Test\GClassTester
   */
  protected function assertThatGClass(GClass $gClass) {
    return new GClassTester($gClass, $this);
  }
  
  /**
   * @return Psc\System\Dir
   */
  public function getTestDirectory($sub = '/') {
    if (!isset($this->testFilesDirectory)) {
      $this->testFilesDirectory = $GLOBALS['env']['root']->sub('tests/files/');
      $this->testFilesDirectory->resolvePath(); // make abs
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

  
  public function getMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE, $cloneArguments = TRUE) {
    $originalClassName = Code::expandNamespace($originalClassName, Code::getNamespace(get_class($this)));
    
    return parent::getMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $cloneArguments);
  }

  public function getMockForAbstractClass($originalClassName, array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE, $mockedMethods = array(), $cloneArguments = TRUE) {
    $originalClassName = Code::expandNamespace($originalClassName, Code::getNamespace(get_class($this)));
    return parent::getMockForAbstractClass($originalClassName, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $mockedMethods, $cloneArguments);
  }
  
}
?>