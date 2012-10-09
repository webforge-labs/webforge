<?php

namespace Webforge\Setup\ConfigurationTester;

use Psc\A;
use Psc\Preg;

/**
 * 
 */
class ConfigurationTester extends \Webforge\Common\BaseObject {
  
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
    if (
      $this->satisfy($operator,
                     $normalizedExpected = $this->normalizeIniValue($iniValue, $iniName),
                     $normalizedActual = $this->normalizeIniValue($actualValue = $this->retriever->retrieveIni($iniName), $iniName)
                     )
    ) {
      return TRUE;
    } else {
      $this->gatherDefect(new IniConfigurationDefect($iniName, $iniValue, $normalizedExpected, $actualValue, $normalizedActual, $operator));
      return FALSE;
    }
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
        throw $this->invalidArgument(1, $operator, 'String', __FUNCTION__); // ==|!=|equal|>|<|<=|>=
    }
  }
  
  protected function normalizeIniValue($iniValue, $iniName) {
    return $this->normalizer->normalize($iniValue, $iniName);
  }
  
  protected function gatherDefect(ConfigurationDefect $defect) {
    $this->defects[] = $defect;
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
  
  /**
   * @TODO refactor to a configurationTester <-> String Object
   */
  public function __toString() {
    $string = "webforge Setup - ConfigurationTester by Psc.\n";
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
}
?>