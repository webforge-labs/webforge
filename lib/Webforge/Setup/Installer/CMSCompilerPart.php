<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Framework\Package\PackageAware;
use Webforge\Code\Generator\GProperty;
use Webforge\Code\Generator\GMethod;

class CMSCompilerPart extends Part implements PackageAware {
  
  public function __construct() {
    parent::__construct('CMSCompiler');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $namespace = $this->package->getNamespace();

    $installer->createClass('Entities\Compiler', Installer::IF_NOT_EXISTS)
      ->parent('Psc\CMS\CommonProjectCompiler')
      ->withGClass(function ($gClass) use ($installer) {

        $gClass->getDocBlock($autoCreate = TRUE)
         ->append(<<<'PHP'
  public function compileNewsEntry() {
    extract($this->help());
    
    return $this->getModelCompiler()->compile(
      $entity('NewsEntry'),
      $defaultId(),
      $property('published', $type('Date')),
      $property('teaser', $type('MarkupText'), $nullable(), $i18n()),
      $property('active', $type('Boolean'))
        ->setDefaultValue(TRUE),
      $property('commentsAllowed', $type('Boolean'))
        ->setDefaultValue(FALSE),
      
      $property('created', $type('DateTime')),
      $property('updated', $type('DateTime'), $nullable()),
      
      $constructor(
        $argument('published'),
        $argument('i18nTeaser', NULL)
      ),
      
      $build($relation($targetMeta('Comment'), 'ManyToMany', 'unidirectional', 'source')), // damit im comment nicht die news_id steht
      $build($relation('ACME\Entities\ContentStream\ContentStream', 'ManyToMany', 'unidirectional', 'source'))
    );
  }
PHP
);
    });

    $command = $installer->createClass('CMS\CompileCommand', Installer::IF_NOT_EXISTS)
      ->parent('Psc\System\Console\ProjectCompileCommand')
      ->getGClass();

    $installer->info('You need to put '.$command.' into your CLI application.');
    $installer->info('Done.');
  }
}
