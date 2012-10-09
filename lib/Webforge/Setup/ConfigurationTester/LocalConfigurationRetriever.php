<?php

namespace Webforge\Setup\ConfigurationTester;

class LocalConfigurationRetriever extends \Webforge\Common\BaseObject implements ConfigurationRetriever {
  
  /**
   * Gibt die ini der lokalen Installation zurück
   *
   * Achtung: auf Debian ist dies wenn dies in PHPUnit ausgeführt wird oder per CLI eine andere als für den Apache
   * deshalb muss das etwas komplizierter gemacht werden, wenn man aus der CLI die INI des Apache checken will
   */
  public function retrieveIni($iniName) {
    return ini_get($iniName);
  }

  public function __toString() {
    return 'LocalConfigurationRetriever';
  }
}
?>