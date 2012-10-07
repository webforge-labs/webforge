<?php

namespace Webforge\Setup;

use Psc\Preg;

class IniValueNormalizer extends \Webforge\Common\BaseObject {
  
  public function normalize($iniValue, $iniName) {
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
}
?>