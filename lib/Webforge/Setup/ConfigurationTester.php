<?php

namespace Webforge\Setup;

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
  
  public function INIs(Array $iniValues) {
    foreach ($iniValues as $iniName => $iniValue) {
      if (!$this->testINI($iniName, $iniValue)) {
        return FALSE;
      }
    }
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
  
  public function getDefects() {
    return $this->defects;
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
   * Das wäre natürlich schöner auszulagern, aber gut
   */
  public function __toString() {
    $string = "webforge Setup - ConfigurationTester by Psc.\n";
    $string .= "\n";
    if (count($this->defects) === 0) {
      $string .= sprintf('OK (%d checks)', count($this->checks));
    } else {
      $string .= sprintf("DEFECTS DETECTED!\nchecks: %d, defects: %d\n", count($this->checks), count($this->defects));
      $string .= "\n";
      $string .= \Webforge\A::join($this->defects, "%s\n");
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