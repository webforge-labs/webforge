<?php

namespace Webforge\Setup\Installer;

use Psc\System\File;
use Psc\System\Dir;

interface Installer {
  
  const IF_NOT_EXISTS = 0x000001;
  
  public function install(Part $part, Dir $destination);
  
  /**
   * Copies the $source to $destionation
   *
   * if source is a file and destination is a dir the $file is copied to $destination
   * if source is a dir and destination is a dir
   * @param Dir|File $source
   * @param Dir|File $source
   */
  public function copy($source, $destination, $flags = 0x000000);
  
  public function write($contents, File $destination, $flags = 0x000000);

  /**
   * @return Psc\System\Dir
   */
  public function getInstallTemplates();
  
  public function execute($cmd);
  
  public function warn($msg);
}
?>