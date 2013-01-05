# Package

Namespace for Package management.

## Package

A package can be seen as a whole application / website / framwork or library. It resolves a class of problems and should be distributed as a whole.
Packages can rely on other packages to work. In concrete a package is a structure of directories with files in it. Those files and code insides this files, defines the package.
Packages can be loaded with the registry, and listed with the registry. Simplepackage is a small implemenation of the Package Interface.

### Names and Slugs

Often there are more than one wording to name a Package itself. To be compatible with composer we have the `identifier` which is just the `vendor`/`slug` (vendor and slug concatenated with a slash).
The `slug` should be a nicename and machine readble. It should contain only file or directory safe characters (of windows systems, too). The vendor name should be a nicename like slug.
The title of the package may be a normal written title for human beings. It may contain non-file and non-directory safe characters.

use:
* the `slug` for database names, directory names, namespaces, class prefixes, everything else technical
* the `identifier` to provide better context for the package with the `vendor` if necessary
* the `title` to display a human readable name 

### Directory Layout conventions

* lib the main directory for your core classes of your package. lib is the psr-0 directory for auto loading from classes
* tests the main directory for your tests for your core classes of your package. tests is the psr-0 directory for auto loaded the tests
  * files put all your resources for your tests here
* etc everything related to configuration
* bin put your executables here. If you have a command line, link cli in root to some executable here for your command interface
* examples code snippets for usage, etc
* resources every file that does not belong to a test, is needed by your application. gives more information, etc.
* application if your package is mainly a library and lives in lib. but you still need a way to seperate a sample application from it, use this directory
* www the document root for your webserver of the project

dont commit these directories. put into your ignore file for source control
* vendor: leave this directory for [composer](http://www.getcomposer.org)!. 
* build: leave this directory for build artifacts
* dist: leave this directory for github to provide distributed resources

### Where to put

* classes: into lib
* templates: resources/templates or into your application directory
* fixtures and other: into tests/files/ try to use generic directories not test-centric directories (global test data)
* configuration of every kind: put into etc! everything! apache, php, js, whatever
* files related to frontend: put into www if access needs to be public. But everything else in lib or application. If nothing matches use resources
* vendor: never put something here, leave it to composer to manage this dir
* files for continous integration, composer, phpunit etc, put into root if single file. Put into resources/build if more complicated

## TODO

Discussion: Package should be subnamespace of Framework, not Setup?