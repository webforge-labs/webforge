# Dependencies

## Psc

to decouple webforge from Psc-Namespace here is a list of required Dependencies (which should be resolved)

* `Webforge\CMS\EnvironmentContainer` uses Psc\Session\Session and Psc\PHP\CookieManager as Implementation
* `Webforge\CMS\Navigation\NestedSetConverter uses `Psc\Data\ArrayCollecton` in emptyCollection()

* `Webforge\Code\Generator` several classes require Exceptions and Utils

* `Webforge\Framework\PscCMSBridge` has several depdencies (of course)
