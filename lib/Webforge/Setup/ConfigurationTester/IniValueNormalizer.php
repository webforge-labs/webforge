<?php

namespace Webforge\Setup\ConfigurationTester;

use Webforge\Common\Preg;

class IniValueNormalizer extends \Webforge\Common\BaseObject {
  
  public function normalize($iniValue, $iniName) {
    if ($iniName === 'error_reporting') {
      return $this->normalizeErrorReporting($iniValue);
    }

    if ($iniValue === 'On') return TRUE;
    if ($iniValue === 'Off') return FALSE;
    
    // suche nach Values die in M oder G (megabyte) angegeben werden können
    $iniValue = $this->normalizeByteSize($iniValue, $iniName);
    
    return $iniValue;
  }
  
  protected function normalizeByteSize($iniValue, $iniName) {
    if (Preg::match($iniValue, '/([0-9]+)\s*([gmk]b?)/i', $m)) {
      $value = (int) $m[1];
      switch (mb_strtolower($m[2])) {
        case 'g':
        case 'gb':
          $value *= 1024*1024*1024;
          break;
        case 'm':
        case 'mb':
          $value *= 1024*1024;
          break;
        case 'k':
        case 'kb':
          $value *= 1024;
          break;
        }
      
      return $value;
    }
    
    return $iniValue;
  }
  
  /**
   * Normalizes the error reporting bitmasks to integer (nicer would be to mask as string, but okay)
   *
   * the value can also be given as a string of bitmask, for example:
   * "E_ALL | E_STRICT"
   *
   * @return int (yet)
   */
  protected function normalizeErrorReporting($errorReporting) {
    if (mb_strpos($errorReporting, 'E_') !== FALSE) {
      $constants = array(
        'E_ERROR'=>E_ERROR,
        'E_WARNING'=>E_WARNING,
        'E_PARSE'=>E_PARSE,
        'E_NOTICE'=>E_NOTICE,
        'E_CORE_ERROR'=>E_CORE_ERROR,
        'E_CORE_WARNING'=>E_CORE_WARNING,
        'E_COMPILE_ERROR'=>E_COMPILE_ERROR,
        'E_COMPILE_WARNING'=>E_COMPILE_WARNING,
        'E_USER_ERROR'=>E_USER_ERROR,
        'E_USER_WARNING'=>E_USER_WARNING,
        'E_USER_NOTICE'=>E_USER_NOTICE,
        'E_STRICT'=>E_STRICT,
        'E_RECOVERABLE_ERROR'=>E_RECOVERABLE_ERROR,
        'E_DEPRECATED'=>E_DEPRECATED,
        'E_USER_DEPRECATED'=>E_USER_DEPRECATED,
        'E_ALL'=>E_ALL
      );
      $operators = array('\|', '\^', '&');
      $rx = '/^('.implode('|', array_keys($constants)).'|'.implode('|',$operators).'|\s)+$/';
      
      if (Preg::match($errorReporting, $rx)) {
        eval('$errorReporting = '.$errorReporting.';');
      } else {
        throw new \InvalidArgumentException(
          sprintf("Parse Error for: \n%s\nIt was detected as an bitmask string. You MUST use E_* Constants and | ^ or & and whitespace - nothing else!",
                  $errorReporting
                 )
        );
      }
    }
    
    // @TODO: it would be nice to give this as a normalized string-bitmask like E_ALL | E_STRICT, etc (is this possible / canonical??)
    return (int) $errorReporting;
  }
}
?>