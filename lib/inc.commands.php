<?php

use Webforge\Code\Generator\ClassCreater;
use Webforge\Code\Generator\CreateClassCommand;
use Webforge\Code\Generator\GFunctionBody;
use Webforge\Code\Generator\GClass;
use Webforge\Setup\Installer\PartsInstaller;
use Webforge\Framework\Package\Package;

use Webforge\Common\JS\JSONConverter;
use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Webforge\Common\String;
use Webforge\Common\CLassUtil;
use Webforge\Console\SymfonyCommandOutputAdapter;
use Webforge\Console\InteractionHelper;

/**
 *
 * $createCommand = function ($name, array|closure $configure, closure $execute, $help = NULL)
 *
 * // argument
 * $arg = function ($name, $description = NULL, $required = TRUE, $multiple = FALSE) // default: required
 *
 * // option
 * $opt = function($name, $short = NULL, $withValue = TRUE, $description = NULL) // default: mit value required
 * $flag = function($name, $short = NULL, $description) // ohne value
 */

$createCommand('install:list-parts',
  array(),
  function ($input, $output, $command) use ($container) {
    $interact = new InteractionHelper($command->getHelper('dialog'), $output);
    $partsInstaller = $container->getPartsInstaller($interact, new SymfonyCommandOutputAdapter($output));
    
    $output->writeln('<info>parts available:</info>');
    
    foreach ($partsInstaller->getParts() as $part) {
      $output->writeln('<info>  '.$part->getName());
    }
    
    return 0;
  },
  'Lists all available parts to install'
);

$createCommand('composer',
  array(
    $arg('composerArguments', 'all parameters passed to composer without --working-dir', $required = TRUE, $multiple = TRUE)
  ),
  function ($input, $output, $command) use ($container) {
    $args = $input->getArgument('composerArguments');
    
    $package = $container->getLocalPackage();
    $vendorDir = $package->getDirectory(Package::VENDOR);
    $composer = 'composer';
    
    system($composer.' --working-dir="'.$vendorDir->getQuotedString().'" '.implode(' ',$args));
    
    $output->writeln('<info>written '.$source.'</info>');
  },
  "Calls composer for the current package (in the right directory)"
);

$createCommand('windows:batch-link',
  array(
    $arg('source', 'the name of the file you want to link from. in 99% of all cases you want to pass .bat with it'),
    $arg('destination', 'the name of the bin you want to link to. (with extension)')
  ),
  function ($input, $output, $command) use ($container) {
    $destination = new File($input->getArgument('destination'));
    $source = new File($input->getArgument('source'));
    
    if ($source->exists()) {
      $output->writeln('<warn>Source: '.$source.' does exist. Will not overwride..</warn>', 1);
      return 1;
    }
    
    try {
      $relativeDestination = clone $destination->getDirectory();
      $relativeDestination->makeRelativeTo($source->getDirectory());
      
      $path = '%~dp0'.((string) $relativeDestination);
      
    } catch (\Exception $e) {
      // use absolute path
      $path = (string) $destination->getDirectory()->resolvePath();
    }
    
    
    $source->writeContents(
      "@echo off\r\n".
      $path.$destination->getName()." %*\r\n"
    );
    
    $output->writeln('<info>written '.$source);
  },
  "Creates a link to another batch/binary file from source to destination"
);


$createCommand('sublime:new-project',
  array(
  ),
  function ($input, $output, $command) use ($container) {
    $package = $container->getLocalPackage();
    
    $project = (object) array(
      'folders'=> array(
        (object) array(
          "path"=> '.',
          "folder_exclude_patterns"=> array("vendor", "build")
        )
      )
    );

    $converter = new JSONConverter();
    $dest = $package->getRootDirectory()->getFile(sprintf('%s-%s.sublime-project', $package->getVendor(), $package->getSlug()))
      ->writeContents($converter->stringify($project));
    
    $output->writeln('<info>written '.$dest.'</info>');
  },
  "Creates an very basic sublime project file"
);

$createCommand('sublime:create-use-completion',
  array(
  ),
  function ($input, $output, $command) use ($container) {
    $folder = new Dir('C:\Users\Philipp Scheit\Dropbox\work\sublime\Packages\Webforge\\');

    $dialog = $command->getHelper('dialog');

    // @TODO if this is asked, then save into a .webforge/settings.json file
    // like: askGlobalConfigurationSetting(sublime.use-complections-directory)
    if (!$folder->exists()) {
        $folder = $dialog->askAndValidate($output, 'In welches Verzeichnis soll die complection file geschrieben werden? (muss existieren)', function ($dir) {
        $dir = Dir::factoryTS($dir);

        if (!$dir->exists()) {
          throw new RuntimeException('Directory does not exist: '.$dir);
        }

        return $dir;
      });
    }

    $converter = new JSONConverter();

    // read
    $useFile = $folder->getFile('use.sublime-completions');
    if ($useFile->exists()) {
      $use = $converter->parseFile($useFile);
    } else {
      $use = (object) array(
        "scope"=> "text.html.basic",
        "completions"=> array(
        )
      );
    }

    $fqn = $dialog->ask($output, 'Wie ist der FQN?');
    $fqn = ltrim($fqn, '\\');

    $alias = $dialog->ask($output, 'Wie ist der Alias ? (optional) ');

    $trigger = $dialog->ask($output, 'Der Name des Triggers ohne "use " davor', ClassUtil::getClassName($fqn));

    // add
    $use->completions[] = (object) array(
      "trigger"=>"use ".$trigger, 
      "contents"=>$alias
        ? sprintf("use %s as %s;", $fqn, $alias)
        : sprintf("use %s;", $fqn)
    );
    
    $useFile->writeContents($converter->stringify($use, JSONConverter::PRETTY_PRINT));
    $output->writeln('<info>written '.$useFile.'</info>');
    $output->writeln('Die Completion kann jetzt mite use '.$trigger.'<tab> benutzt werden');
  },
  "Creates an new PHP use completion for sublime"
);
