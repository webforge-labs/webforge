# (Parts) Installer

## Create a new Part

  - use webforge `install::create-part $name`
  - add the part to the `Webforge\Framework\Container::getPartsInstaller()` function

## TODO

  - use the command pattern to implement:
    - log what is happening while running a part
  - create a simple composite part (you can still use: `$installer::install` to nest)
  - refactor more file system commands into the installer
  - refactor a "location finder" or similar to resolve dependencies like "webforge resources" or "installer templates" etc
  - allow code expanding. For example: adding the new class to the install:create-part when code needs to be injected to the container or other php files
  
## Parts (TODO)
  - Install Compiler for Entities
  - Install CMS (Dir, JSBoilerplate, index.php, api.php)
  - Install CMS\Main