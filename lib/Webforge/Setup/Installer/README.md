# (Parts) Installer

## Create a new Part

  - extend the abstract class `Webforge\Setup\Installer\Part` name: `DoSomethingPart`
    - call `parent::__construct('DoSomething');`
  - add the part to the `Webforge\Framework\Container::getPartsInstaller()` function
  
