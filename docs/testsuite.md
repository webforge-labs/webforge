# How to install a TestSuite

Its assumend that you have webforge-devtool installed and your package registered with webforge.

Its as easy as:
```
webforge install:part InstallTestSuite
```
per default it will use PHPUnit and create a phpunit.xml.dist in your root for you. You have to create a bootstrap with install:part because it will use the bootstrap.php in root per default.
You can then create your first test (e.g. for the `ACME\Common\Container`-class) with:
```
webforge create-test ACME\Common\Container
```
If you have the webforge sublime plugin installed. Create the class before and execute `webforge: create test for class` (f6) in your command palette. When your package is configured for composer autoloading everything will be fine and your test was created by webforge.