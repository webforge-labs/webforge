<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Webforge\Code\Generator\GClass;

interface Installer {
  
  const IF_NOT_EXISTS = 0x000001;

  public function install(Part $part, Dir $destination);
  
  /**
   * Copies the $source to $destination
   *
   * if source is a file and destination is a dir the $file is copied to $destination
   * if source is a dir and destination is a dir
   * @param Dir|File $source
   * @param Dir|File $source
   */
  public function copy($source, $destination, $flags = 0x000000);
  
  public function write($contents, File $destination, $flags = 0x000000);
  
  public function writeTemplate(File $template, File $destination, Array $vars = array(), $flags = 0x000000);
  
  public function createDir($targetSub);

  /**
   * @return Webforge\Code\Generator\CreateClassCommand
   */
  public function createClass($relativeClassName, $flags = 0x000000, File $destination);


  public function addCLICommand(GClass $gClass);

  /**
   * @return Webforge\Common\System\Dir
   */
  public function getInstallTemplates();

  /**
   * @return Webforge\Common\System\File
   */
  public function getInstallTemplate($relativePath);
  
  public function execute($cmd);
  
  public function warn($msg);

  /**
   * @return string input from user or return value from $validator if given
   */
  public function ask($question, $default = NULL, \Closure $validator = NULL, $attempts = FALSE);

  /**
   * @return bool
   */
  public function confirm($question);
  
  /**
   * Outputs a suggestion / or message
   */
  public function info($msg);
}
