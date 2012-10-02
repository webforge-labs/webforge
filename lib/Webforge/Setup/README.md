# Setup

Setup is the namespace for configuration / installer and environment related settings.

## ConfigurationTester
The ConfigurationTester is able to check configuration of the environment (currently PHP-ini values) to a set of fixed values. The php.ini settings can be received through the ConfigurationRetriever (for example from a apache dump-script).

```php
use Webforge\Setup\ConfigurationTester;

$t = new ConfigurationTester();
$t->INI('mbstring.internal_encoding', 'utf-8');
```

You can use several operators to compare values
```php
$t->INI('post_max_size','2M', '>=');
$t->INI('post_max_size',1024, '<');
```

values like "2M" (filesizes) will get normalized, so that it is natural to compare them.
```php
// if ini_get('post_max_size') is "2M" or 2097152 doesn't matter
$t->INI('post_max_size',2*1024*1024);
$t->INI('post_max_size','2M');
```

You can use the ConfigurationTester to test your webserver (or other remotes) PHP-ini values:
```php
use Webforge\Setup\ConfigurationTester;

$t = new ConfigurationTester(new RemoteConfigurationRetriever('http://localhost:80/dump-inis.php'));
```

put dump-inis.php into webroot with this contents:
```php
<?php
print json_encode(ini_get_all());
?>
```

You can get the results of the checks, with retrieving the defects. They are instances from ConfigurationDefect Class and can be converted to String to verbose their failure:
```php
if (count($t->getDefects()) > 0) {
  throw new \RuntimeException('Please check your Config: '.implode("\n", $t->getDefects()));
}
```
You could get nicer formatted output with the ConfigurationTester::__toString(). This may change in the future for a ConfigurationTesterPrinter or something..
```php
print $t;
```