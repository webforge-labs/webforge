<?php

namespace Webforge\Setup\Installer;

interface Installer {
  
  const IF_NOT_EXISTS = 0x000001;
  
  /**
   * Copies the $source to $destionation
   *
   * if source is a file and destination is a dir the $file is copied to $destination
   * if source is a dir and destination is a dir
   * @param Dir|File $source
   * @param Dir|File $source
   */
  public function copy($source, $destination, $flags = 0x000000);

}
?>