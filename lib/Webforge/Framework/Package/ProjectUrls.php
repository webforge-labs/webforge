<?php

namespace Webforge\Framework\Package;

use Webforge\Common\Url;
use Webforge\Configuration\Configuration;
use Webforge\Framework\Project;
use Webforge\Framework\Exception as DefaultException;

class ProjectUrls {

  protected $urls = array();
  protected $hostConfig;

  public function __construct(Configuration $hostConfig) {
    $this->hostConfig = $hostConfig;
  }

  public function get($type, Project $project) {
    if ($type === 'cms') {
      return $this->getCmsBaseUrl($project);
    } else {
      return $this->getBaseUrl($project);
    }
  }

  /**
   * @return Psc\URL\SimpleURL
   */
  protected function getBaseUrl(Project $project) {
    if (!isset($this->urls['base'])) {
      try {

        if (($url = $project->getConfiguration()->get(array('url', 'base'))) != NULL) {

        } elseif (($pattern = $this->hostConfig->get(array('url', 'hostPattern'))) != NULL) {
          $url = sprintf($pattern, $project->getLowerName(), $project->getName());

        } else {
          throw new \Exception('No Config Variables Found');
        }

        /* dummy check for devs */
        if (mb_strpos($url,'http://') !== 0 || mb_strpos($url,'https://') !== 0) {
          $url = 'http://'.$url;
        }

        $this->urls['base'] = new Url($url);

      } catch (\Exception $e) {
        throw new DefaultException('Project-Configuration: cannot configure baseUrl. set url.base in projectPackage config or url.hostPattern in host-config', 0);
      }
    }

    return clone $this->urls['base'];
  }

  protected function getCMSBaseUrl(Project $project) {
    if (!isset($this->urls['cms'])) {
      $baseUrl = $this->getBaseUrl($project);

      if ($project->getConfiguration()->get(array('project','cmsOnly')) === TRUE) {
        return $this->urls['cms'] = $baseUrl;
      }

      if ($project->getConfiguration()->get(array('project','cmsUrl')) === 'cms') {
        $cmsUrl = clone $baseUrl;
        $cmsUrl->addPathPart('cms');
        return $this->urls['cms'] = $cmsUrl;
      }

      $hostParts = $baseUrl->getHostParts();
      array_unshift($hostParts,'cms');
      $baseUrl->setHostParts($hostParts);

      return $this->urls['cms'] = $baseUrl;
    }

    return clone $this->urls['cms'];
  }
}
