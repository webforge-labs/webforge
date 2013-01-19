<?php

namespace Webforge;

use Webforge\Common\System\Dir;

/**
 * @group acceptance
 */
class InstallAndBootstrapPackageTest extends \Webforge\Code\Test\Base {
  
  public static $installed;
  
  protected $dir;

  public static function setUpBeforeClass() {
    self::$installed = FALSE;
  }
  
  public function setUp() {
    $this->dir = $this->getTempDirectory('bootstrap-package/');
    $this->composerDir = $this->dir->sub('Umsetzung/base/src/');
    
    if (!self::$installed) {
      //$this->prepareEmptyDirectory($this->dir);
      $this->copyOldStyleProject($this->dir);
      $this->installPackageWithComposer($this->composerDir);
    }
  }
  
  protected function copyOldStyleProject(Dir $dir) {
    $this->getTestDirectory()->sub('packages/PscOldStyleProject/')
      ->copy($dir);
  }

  protected function installPackageWithComposer(Dir $dir) {
    $this->composerInstall($dir);
  }
  
  protected function prepareEmptyDirectory(Dir $dir) {
    $dir->create();
    $dir->wipe();
    
    return $dir;
  }
  
  protected function composerInstall(Dir $dir) {
    // use prefer-dist to install from cache if possible
    $this->execute('cd '.$dir.' && composer install --prefer-dist');
  }
  
  protected function execute($cmd) {
    system($cmd, $ret);
  
    $this->assertEquals(0, $ret, sprintf("'%s' did not return 0.", $cmd));
    return $ret;
  }

  public function testPackageInstalledFromComposer_vendorDependencyWasCreated() {
    $this->assertFileExists((string) $this->composerDir->getFile('composer.lock'));
    
    $this->assertDirectoryExists($this->composerDir->sub('vendor/pscheit/psc-cms/'));
  }
  
  public function testBootstrapCreatedFromWebforgeDoesWorkWithoutErrors() {
    $phpFile = $this->writeTestPHPFileToHtdocs($this->dir);
    
  }
  
  protected function writeTestPHPFileToHtdocs(Dir $dir) {
    $file = $dir->sub('Umsetzung/base/htdocs/')->create()->getFile('inc.bootstrap-test.php');
    
    $file->writeContents(<<<'PHP'
<?php

$ds = DIRECTORY_SEPRATOR;

require __DIR__.$ds.'..'.$ds.'src'.$ds.'bootstrap.php';

if (!class_exists('Psc\PSC')) {
  throw new Exception('Psc\PSC class cannot be found - autoloading fails.');
}

if (!class_exists('Webforge\Common\System\Dir')) {
  throw new Exception('Webforge\Common\System\Dir class cannot be found - autoloading fails.');
}

if (!isset($GLOBALS['env']['root']) || !($GLOBALS['env']['root'] instanceof \Webforge\Common\System\Dir)) {
  throw new Exception('GLOBALS[env][root] is not a directory! - bootstrap fails');
}

?>
PHP
    );
  }
}
?>