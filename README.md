# Webforge
a content management framework for easy to build and individual content management systems.

[![Build Status](https://secure.travis-ci.org/pscheit/webforge.png)](http://travis-ci.org/pscheit/webforge)

Webforge aims to be a framework for consumers and developers at the same time. It helps developers to build their own individual cms for the needs of their customers. The customer gains all benefits of a ready-to-use cms without losing the opportunity to create new features.

Webforge is a rename from Psc - CMS, which has a much larger codebase. But the code needs a lot of restructuring and refactoring, so that I decided to do it step by step.

## installation
Use [Composer](http://getcomposer.org) to install the framework.
```
php composer.phar install
```

to run the tests use:
```
phpunit
```

It's optional to configure the host-config for Psc - CMS. This might change in future, but it is already planned to move the whole autoloading to composer (which is not the case for Psc\* classes)

## roadmap
  - step by step integration from boilerplate code from psc-cms into this repo
  - build better modules out from psc-cms
  - write better and english documentation for these modules
  - [Refactor a lot](http://wiki.ps-webforge.com/psc-cms:start#refactoring-roadmap)
  - search for contributors and people with interest

##resources
[Psc-CMS - Documentation in german](http://wiki.ps-webforge.com/psc-cms:start)

