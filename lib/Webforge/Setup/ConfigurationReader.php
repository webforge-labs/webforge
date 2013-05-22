<?php

namespace Webforge\Setup;

use Webforge\Common\System\File;

class ConfigurationReader {

  protected $scope = array();

  public function fromPHPFile(File $phpFile) {
    extract($this->scope);

    require $phpFile;
      
    if (!isset($conf) || !is_array($conf)) {
      throw new ConfigurationReadingException(
        sprintf("Config-File '%s' does not define \$conf. Even if its empty it should define \$conf as empty array.", $phpFile)
      );
    }
    
    return new Configuration($conf);
  }

  public function setScope(Array $scope) {
    $this->scope = $scope;
    return $this;
  }
}
