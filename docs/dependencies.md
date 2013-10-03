# Dependencies

## Psc

to decouple webforge from Psc-Namespace here is a list of required Dependencies (which should be resolved)

* `Webforge\CMS\EnvironmentContainer` uses Psc\Session\Session and Psc\PHP\CookieManager as Implementation
* `Webforge\Setup\ConfigurationTester\RemoteConfigurationRetriever` refactor to use guzzle
* `Webforge\Code\Generator` several classes require Exceptions and Utils
* Webforge\Setup\Installer\Command refactor to use symfony event system
* `Webforge\Framework\PscCMSBridge` has several depdencies (of course)
