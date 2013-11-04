# directory locations for packages

Have you ever tackled a project where you could not find something in the directory structure? Sometimes folder naming in packages is very inconsistent. Some name the location for PSR-0 classes `lib` others call it `src` other put their classes in `something/you/would/not/find/class.php`.  
Webforge tries to configure a package with the composer.json from composer on package level so that you won't have to deal with directory locations in your application. You should be able to move directories, change the composer.json with no need to change your application code.

Have a look at this (reduced) composer.json

```json
{
  "name":"acme/blog",
  "autoload":{
    "psr-0":{
      "ACME\Blog":["lib/", "tests/"]
    }
  },
  "extra":{
    "branch-alias":{
      "dev-master":"1.0.x-dev"
    },

    "directory-locations": {
      "etc": "etc/",
      "tests": "tests/",
      "assets": "resources/assets/",
      "cache": "files/cache/"
    }
  }
}
```

the extra configuration part is for webforge. When your package is read from the composer package reader it will analyse this structure for `directory-locations`. You are then able to do:
```php
$blogPackage->getDirectory('etc');
// or
$blogPackage->getDirectory('assets');
```

You can then start off from there with normal `Webforge\Common\System\Dir` syntax:
```php
$blogPackage->getDirectory('cache')->sub('templates/mustache/'); // expanded to: $package->getRootDirectory()->sub('files/cache/')->sub('templates/mustache')
```

## directory locations for projects

You can retrieve the directory-locations aliases in a project with:
```php
$blogProjectPackage->dir('cache');
```

For example a configuration for the doctrineContainer ($dcc)
```php
$dcc->initDoctrine(
  $this->project->getConfiguration()->req('db'),
  array($this->project->dir('doctrine-entities'))
);
```

## pre defined locations

For a current set of ALL locations, have a look into: `Webforge\Framework\DirectoryLocations`. But here are a few (important ones):

  - **lib**: the one and only location for your class files structured as PSR-0 (starting with your own primary namespace)
  - **tests**: same structure as lib but for classes that should be run by your testsuite
  - **test-files**: store location for fixtures and other resources used by your tests (can be everything). Should be checked into vcs!
  - **bin**: the place for executables or cli php files
  - **etc**: a location to store your configuration in. possible subs are: apache2 and auth (htpasswd files)
  - **cache**: location to store caches and temporary files (should never be checked into your vcs or deployed). Always use a sub directory of this
    - subs: tmp, mustache, doctrine
  - **tpl**: all the templates of your project (webforge/view will use this)
  - **docs**: the place to store (developer)-documentation.
  - **vendor**: the vendor directory from composer (never put something there manually). Should not be checked in vcs
  - **build**: a temporary directory for your output of the php/application layer (not build assets). Should never be checked in vcs.
    - subs: **coverage** for phpunit coverage reports
  - **logs**: store your application / apache - logs here (should never be checked in)

  - **www**: the location for your application document root (apache could point to that)
  - **cms-www**: the location for your admin panel document root
  - **cms-uploads**: user files that belong to your current project

  - **assets-src**: the place for js, css and image files used in your frontend, and everything else which is not a  php class
  - **assets-built**: same as assets-src (same subs) but
    - possible subs: img, js, css

  - **resources**: if none of the above matches, but it here into a subdirectory

  - **doctrine-entities**: used by the doctrine container (webforge/doctrine) for the first location of your entities for the annotation reader
  - **doctrine-proxies**: used by the doctrine container to give the location for the automatic-generated proxies from doctrine

[have a look at the package documentation](package.md) to see a directory layout example
