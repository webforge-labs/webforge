<?php

namespace Webforge\Setup\ConfigurationTester;

use Webforge\Common\ArrayUtil as A;
use Webforge\Common\Preg;
use InvalidArgumentException;

/**
 * 
 */
class ConfigurationTester {
  
  /**
   * Die Extension dessen IniValues nicht gecheckt werden sollen
   *
   * @var array
   */
  protected $skipExtensions = array();
  
  protected $defects = array();
  
  protected $checks = array();
  
  /**
   * @var Webforge\Setup\IniValueNormalizer
   */
  protected $normalizer;
  
  /**
   * @var Webforge\Setup\ConfigurationRetriever
   */
  protected $retriever;
  
  public function __construct(ConfigurationRetriever $retriever = NULL, IniValueNormalizer $normalizer = NULL) {
    $this->setNormalizer($normalizer ?: new IniValueNormalizer());
    $this->setRetriever($retriever ?: new LocalConfigurationRetriever());
  }
  
  /**
   * parses an Array of INI Values
   *
   * 
   */
  public function INIs(Array $iniValues) {
    foreach ($iniValues as $iniName => $iniValue) {
      list($operator, $iniValue) = $this->expandIniValue($iniValue);
      $this->INI($iniName, $iniValue, $operator);
    
    }
  }
  
  /**
   * Parses an iniValue for an optional parameter
   *
   * @param string $iniValue [operator] value
   * @return list($operator, $iniValue)
   */
  public function expandIniValue($iniValue, $operator = '==') {
    $operators = array('==', 'equal', '!=', '>=', '=>', '<=', '=<', '<', '>');
    
    $rx = '/^('.implode('|',$operators).')\s*(.*?)$/';
    $m = array();
    if (Preg::match($iniValue, $rx, $m) > 0) {
      list($m, $operator, $iniValue) = $m;
    }
    
    return array($operator, $iniValue);
  }
  
  public function INI($iniName, $iniValue, $operator = '==') {
    $this->checks[] = array($iniName, $iniValue, $operator);
    
    if ($this->shouldBeSkipped($iniName)) {
      // gatherMessage would be cool
      return TRUE;
    }
    
    if (
      // e.g. post_max_size (2M) > 8M => false
      $this->satisfy($operator,
                     $normalizedActual = $this->normalizeIniValue($actualValue = $this->retriever->retrieveIni($iniName), $iniName),
                     $normalizedExpected = $this->normalizeIniValue($iniValue, $iniName)
                     )
    ) {
      return TRUE;
    } else {
      $this->gatherDefect(new IniConfigurationDefect($iniName, $iniValue, $normalizedExpected, $actualValue, $normalizedActual, $operator));
      return FALSE;
    }
  }
  
  public function shouldBeSkipped($iniName) {
    list($extension) = explode('.', $iniName, 2);
    if ($extension && in_array($extension, $this->skipExtensions)) {
      return TRUE;
    }
    
    return FALSE;
  }
  
  protected function satisfy($operator, $one, $other = NULL) {
    // add here + add to operators in expandIniValue
    switch($operator) {
      case '==':
      case 'equal':
        return $one == $other;
      case '!=':
        return $one != $other;
      
      case '>':
        return $one > $other;
      case '>=':
      case '=>':
        return $one >= $other;
      case '<=':
      case '=<':
        return $one <= $other;
      case '<':
        return $one < $other;
      
      default:
        throw new InvalidArgumentException('operatory is expected to be a string of type ==|!=|equal|>|<|<=|>=');
    }
  }
  
  protected function normalizeIniValue($iniValue, $iniName) {
    return $this->normalizer->normalize($iniValue, $iniName);
  }
  
  protected function gatherDefect(ConfigurationDefect $defect) {
    $this->defects[] = $defect;
  }
  
  
  /**
   * Does not validate INI-Values from this extension
   *
   * @param string $extensionName (lowercase)
   */
  public function skipExtension($extensionName) {
    $this->skipExtensions[] = $extensionName;
    return $this;
  }
  
  /**
   * @return array ConfigurationDefects
   */
  public function getDefects() {
    return $this->defects;
  }
  
  /**
   * @return bool
   */
  public function hasDefects() {
    return count($this->defects) > 0;
  }
  
  /**
   * @TODO refactor to a configurationTester <-> String Object
   */
  public function __toString() {
    $string = "webforge Setup - ConfigurationTester by pscheit.\n";
    $string .= (string) $this->retriever."\n";
    $string .= "\n";
    if (count($this->defects) === 0) {
      $string .= sprintf('OK (%d checks)', count($this->checks));
    } else {
      $string .= sprintf("DEFECTS DETECTED!\nchecks: %d, defects: %d\n", count($this->checks), count($this->defects));
      $string .= "\n";
      $string .= A::join($this->defects, "%s\n");
    }
    return $string;
  }
  
  // @codeCoverageIgnoreStart
  /**
   * @param Webforge\Setup\ConfigurationRetriever $retriever
   */
  public function setRetriever(ConfigurationRetriever $retriever) {
    $this->retriever = $retriever;
    return $this;
  }
  
  /**
   * @return Webforge\Setup\ConfigurationRetriever
   */
  public function getRetriever() {
    return $this->retriever;
  }

  /**
   * @param Webforge\Setup\IniValueNormalizer $normalizer
   */
  public function setNormalizer(IniValueNormalizer $normalizer) {
    $this->normalizer = $normalizer;
    return $this;
  }
  
  /**
   * @return Webforge\Setup\IniValueNormalizer
   */
  public function getNormalizer() {
    return $this->normalizer;
  }
  // @codeCoverageIgnoreEnd
}
