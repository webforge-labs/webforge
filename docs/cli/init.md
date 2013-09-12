# webforge init commands

Sometimes it's a hassle to setup a whole package from scratch. If you want to start off a little bit faster use the webforge init command:

  1. create a repository (locally or on github)
  1. (optional) create a readme for your project
  1. made up your mind for the name and start with `webforge init` in the wanted root of your package


## features

  - asks you basic things about your application (name, namespace, autoloading locations)
  - creates a full functional composer.json for you

## TODO (enhc)

  - create the repository with the github api
  - should ask for additional requirements like the webforge/testplate while init composer
  - initialize the readme if it's not avaible (easy pick)
  - register automatically for travis (if initialized)
  - register automaticcaly for packagist (if initialized)
  - run other commands from webforge parts installer
