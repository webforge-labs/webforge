# Webforge
a content management framework for easy to build and individual content management systems.

[![Build Status](https://secure.travis-ci.org/pscheit/webforge.png)](http://travis-ci.org/pscheit/webforge)

Webforge aims to be a framework for consumers and developers at the same time. It helps developers to build their own individual cms for the needs of their customers. The customer gains all benefits of a ready-to-use cms without losing the opportunity to create new features.

## installation (quick)
Use [Composer](http://getcomposer.org) to install the framework.
```
composer create-project -v --prefer-source --stability=dev webforge/webforge webforge dev-master
```

link ``bin\webforge`` to a global binary ``webforge``

to run the tests use:
```
phpunit
```

## installation

http://wiki.ps-webforge.com/psc-cms:dokumentation:core

It's optional to configure the host-config, but recommended.

## roadmap
  - implement pretty printing for the GClass
  - write better and english documentation for these modules
  - [Refactor a lot](http://wiki.ps-webforge.com/psc-cms:start#refactoring-roadmap)
  - search for contributors and people with interest

##resources
[Psc - CMS - Documentation in german / half english](http://wiki.ps-webforge.com/psc-cms:start)
For module documentation see the README files in namespace directories in ``lib/``
