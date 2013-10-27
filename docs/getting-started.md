# getting started

Sometimes it's a hassle to setup a whole package from scratch. If you want to start off a little bit faster use the webforge init command:

  1. create a new directory for your package, named with `vendor`-`package` (in lowercase)
  1. (optional) create a README.md for your project (you can use the readme created from github as default)
  1. start with `webforge init .` in the created directry (named `%root%`) from your package

## Create an new directory with your package name

I think you may have done this before... But: pick a nice technical slug for your project once and then **STICK TO IT**. There are so many projects that have different names in all variants for the same thing. Use one name for your project as a slug, which contains only hyphen 0-9 and a-z. Ideally lowercase. I'll refer to this name as ''slug'' or ''package-slug''. Pick another name for your project which is used for your customer view. I'll refer to this as the project title. **AND NOW STICK TO IT**. \\
E.g. [[http://packagist.org|packagist]] will be pissed off when you want to rename your package once submitted. So think about this package slug, you will write it a dozen times.\\
This directory is called the ''package directory'' or ''project directory''.

Name your directory in the format: `vendor-name`-`package-slug`. You collegues will thank you for that.

## Create a README.md

```
acme-blog
====================

A super blog to allow ACME to blog in the world wide web.
```
[Curious what ACME is?](http://en.wikipedia.org/wiki/Acme_Corporation)

## webforge init

It's assumed that you have [webforge/devtool](https://github.com/webforge-labs/webforge-devtool) installed and configured. If you haven't done this. Do this now to follow allong (you won't regret it).

cd to your nicely named directory and execute
```
webforge init .
```
the last parameter means just current directory.
Webforge will ask you a lot about your project and will create a composer.json for you, if you want to. I recommend to do that.

After that your composer.json will look something similar to:

```json
    "name": "acme/blog"
    "autoload": {
        "psr-0": { 
            "ACME\\Blog": ["lib/", "tests/"]
        }
    },
    "minimum-stability": "dev",
    "require": {
      "webforge/common": "dev-master"
    }
```
Please be sure that your name is like `vendor`/`package-slug` (the same as your directory).

Webforge asks to register the package with webforge. Please do that. It gives you the ability to install nearly everything with webforge's help. 
  
  * create classes
  * create test classes
  * create a CLI
  * create a ProjectConsole
  * init Configuration
  * install a TestSuite
  * create a JS Boilerplate
  * create your apache configuration
  * synchronize with git(hub)
  * release your package

and much more. But to help you with that the package needs to be registered, so webforge can find it on your hard disk.

## whats next

We are now ready to [bootstrap](bootstrap.md) our application.
