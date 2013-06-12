<?php

namespace Webforge\Framework;

interface Project {

  // i18n
  /**
   * @return array
   */
  public function getLanguages();

  /**
   * @return strring
   */
  public function getDefaultLanguage();

  /**
   * @return Webforge\Common\System\Dir
   */
  public function getRootDirectory();

  // project status and env
  public function isStaging();

  public function isDevelopment();

  public function getStatus();
}
