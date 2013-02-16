<?php

namespace Webforge;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Psc\System\Console\Process;
use Webforge\Common\System\Util as SystemUtil;

/**
 * @group acceptance
 */
class InstallAndBootstrapPackageTest extends \Webforge\Code\Test\Base {
  
  public static $installed;
  
  protected $dir, $composerDir, $src;

  public static function setUpBeforeClass() {
    self::$installed = FALSE;
  }
  
  public function setUp() {
    // if false: its not recreating the whole directory,
    // its using update on previous test-created dir and deletes some files in prepareempty
    // just a convenience switch: ALWAYS put this to TRUE afterwards
    $this->realAcceptance = TRUE;
    
    $this->dir = $this->getTempDirectory('bootstrap-package/');
    $this->composerDir = $this->dir->sub('Umsetzung/base/src/');
    $this->src = $this->dir->sub('Umsetzung/base/src/');
    
    if (!self::$installed) {
      $this->prepareEmptyDirectory($this->dir);
      $this->copyOldStyleProject($this->dir);
      $this->installPackageWithComposer($this->composerDir);
      $this->createBootstrapWithWebforge($this->composerDir);
      self::$installed = true;
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
    if ($this->realAcceptance) {
      $dir->wipe();
    } else {
      $dir->getFile('Umsetzung/base/src/bootstrap.php')->delete();
      $dir->getFile('Umsetzung/base/src/package.boot.php')->delete();
    }
    
    return $dir;
  }
  
  protected function composerInstall(Dir $dir) {
    if ($this->realAcceptance) {
      $this->execute('cd '.$dir.' && composer install --prefer-dist');
    } else {
      $this->execute('cd '.$dir.' && composer update');
    }
  }
  
  public function testPackageInstalledFromComposer_vendorDependencyWasCreated() {
    $this->assertFileExists((string) $this->composerDir->getFile('composer.lock'));
    $this->assertDirectoryExists($this->composerDir->sub('vendor/pscheit/psc-cms/'));
  }
  
  public function testWrittenBootstrapTestFileReturnsOKForBootstrapCreatedFromWebforge() {
    $phpFile = $this->writeTestPHPFileToHtdocs($this->dir);
    
    $process = $this->runPHPFile($phpFile);
    
    list($status, $code, $message, $stack) = json_decode($process->getOutput());

    if ($status != 'ok') {
      switch ($code) {
        case 1:
          $reason = 'autoloading (composer) was not successful';
          break;
        
        case 5:
          $reason  = 'bootstrap.php was not found';
          break;
        
        case 2:
        case 3:
          $reason = 'global variable not set';
          break;
        
        default:
          $reason = 'unknown (maybe php error)';
      }
      
      $this->fail('Bootstrap test failed because: '.$reason."\n".$message."\n\n".$stack);
    }
    
    $this->assertEquals('ok', $status);
  }
  
  protected function writeTestPHPFileToHtdocs(Dir $dir) {
    $file = $dir->sub('Umsetzung/base/htdocs/')->create()->getFile('inc.bootstrap-test.php');

    $php = <<<'PHP'
<?php

$ds = DIRECTORY_SEPARATOR;

try {

PHP;

    $php .= $this->generateTestBootstrapFile();
    $php .= $this->generateTestComposerAutoloading();
    $php .= $this->generateTestPscContainerIsRegisteredAsGlobal();

    $php .= <<<'PHP'

} catch (\Exception $e) {
  print json_encode(array('exception', $e->getCode(), $e->getMessage(), $e->getTraceAsString()));
  return 1;
}

print json_encode(array('ok', 0, 'everything okay', NULL));
return 0;
?>
PHP;
    
    $file->writeContents($php);
    
    return $file;
  }
  
  protected function generateTestBootstrapFile() {
    return <<<'PHP'
  $bs = __DIR__.$ds.'..'.$ds.'src'.$ds.'bootstrap.php';
  
  if (!file_exists($bs)) {
    throw new Exception('the bootstrap file: '.$bs.' was not found', 5);
  }
  
  require $bs;
PHP;
  }

  protected function generateTestComposerAutoloading() {
    return <<<'PHP'
  if (!class_exists('Psc\PSC')) {
    throw new Exception('Psc\PSC class cannot be found - autoloading fails.', 1);
  }
  
  if (!class_exists('Webforge\Common\System\Dir')) {
    throw new Exception('Webforge\Common\System\Dir class cannot be found - autoloading fails.', 1);
  }
PHP;
  }
  
  protected function generateTestPscContainerIsRegisteredAsGlobal() {
    return <<<'PHP'
  if (!isset($GLOBALS['env']['root']) || !($GLOBALS['env']['root'] instanceof \Webforge\Common\System\Dir)) {
    throw new Exception('GLOBALS[env][root] is not a directory!', 2);
  }
  
  if (!isset($GLOBALS['env']['container'])) {
    throw new Exception('GLOBALS[env][container] is not defined!', 3);
  }
  
  if (!($GLOBALS['env']['container'] instanceof \Psc\CMS\Container)) {
    throw new Exception('GLOBALS[env][container] is not a Psc CMS Container!', 3);
  }
PHP;
  }
  
  public function testPHPUnitInnerTestIsSuccessful() {
    $unitTest = $this->copyPHPUnitTest();
    
    $process = Process::build($this->which('phpunit'))
                  ->addOption('no-configuration')
                  ->addOption('stop-on-failure')
                  ->addOption('no-globals-backup') // because this would destroy our "same same" acceptance tests
                  ->addOption('bootstrap', $this->src->getFile('bootstrap.php'))
                  ->addArgument((string) $unitTest)
                  ->end();
                  
    $this->assertRun($process, 0, 'PHPUnit Inner Test was not successful');
  }
  
  /**
   * @return file
   */
  protected function copyPHPUnitTest() {
    $file = new File(__DIR__.DIRECTORY_SEPARATOR.'BootstrapAcceptanceInnerTestCase.php');
    $file->copy($testFile = $this->src->getFile('BootstrapTest.php'));
    
    return $testFile;
  }
  

  protected function createBootstrapWithWebforge(Dir $composerDir) {
    $process = Process::build($this->which('webforge'))
                ->addArgument('install:part')
                ->addArgument('CreateBootstrap')
                ->setWorkingDirectory($composerDir)
                ->end();
    
    $this->assertRun($process, 0);
  }

  protected function execute($cmd) {
    system($cmd, $ret);
  
    $this->assertEquals(0, $ret, sprintf("'%s' did not return 0.", $cmd));
    return $ret;
  }
  
  protected function assertRun(Process $process, $returnCode, $message = '') {
    $this->assertEquals($returnCode, $process->run(), $message.": cmd:\n".$process->getCommandLine()."\n\n".$process->getOutput()."\n\n".$process->getErrorOutput());
  }
  
  protected function which($cmd) {
    $finder = new \Symfony\Component\Process\ExecutableFinder;
    
    if ($file = $finder->find($cmd)) {
      return new File($file);
    }
    
    if (\Psc\PSC::isTravis() && $cmd === 'webforge') {
      return Dir::factoryTS(__DIR__)->sub('../../../bin/webforge');
    }
    
    $this->assertInstanceOf(
      'Webforge\Common\System\File',
      $bin = \Psc\System\System::which($cmd),
      'cmd cannot be whiched: '.$cmd.' path: '.var_dump(getenv('PATH'))
    );
    
    return $bin;
  }
}
?>