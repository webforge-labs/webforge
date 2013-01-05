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
  the `slug` for database names, directory names, namespaces, class prefixes, everything else technical
  the `identifier` to provide better context for the package with the `vendor` if necessary
  the `title` to display a human readable name 

## TODO

Discussion: Package should be subnamespace of Framework, not Setup?