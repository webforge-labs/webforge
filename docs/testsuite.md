# How to install a TestSuite

Its assumend that you have webforge-devtool installed, your package registered with webforge and created a bootstrap.

I recommend to start of with the [webforge-testplate](https://github.com/webforge/testplate). Allthough if you have no clue how to write tests, this is a very good start to get everything integrated. 

## installation

Its as easy as:
```
webforge install:part InstallTestSuite
```
per default it will use PHPUnit and create a phpunit.xml.dist in your root for you. You have to create a bootstrap with install:part because it will use the bootstrap.php in root per default.
Testplate will install some dependencies, but it will only installed in your composer-dev section, so your application will not be bloated.

## configuration

When you have called `$bootLoader->registerRootDirectory()` on your bootloader in the bootstrap.php, you'll get a little helper to get your fixture-files in `tests\files` without a hassle:

```php
class MyTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    parent::setUp();

    $this->testDir = $this->getTestDirectory('my-test-data/'); // returns a Webforge\Common\System\Dir pointing to `%root%/tests7files/my-test-data/`
  }
}

```

## usage

You can then create your first test (e.g. for the `ACME\Common\Container`-class) with:
```
webforge create-test ACME\Common\Container
```
If you have the webforge sublime plugin installed. Create the class before and execute `webforge: create test for class` (f6) in your command palette. When your package is configured for composer autoloading everything will be fine and your test was created by webforge.

To see more usage and more features what testplate can do for you [refer to the testplate documentation](https://github.com/webforge-labs/webforge-testplate).

## what's next

Start developing your application. Write tests as much as you can. Use install:part to integrate with travis or packagist. 

If you want further help from webforge you can [start using the framework](framework.md).
If you want to release your package you can [release your package with webforge](release.md).
