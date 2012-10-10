# Ubiquitous Language 
A language structured around the domain model and used by all team members to connect all the activities of the team with the software. (Eric Evans)

## Definitions
This is a list of words and definitions of the language.

### class FQN
The class Fully Qualified Name is mostly given as a string. It refers to a file with an absolute namespace. But no \ is prepended before the Namespace.

```php
$fqn1 = "Webforge\Code\Test\Base";
$fqn2 = "stdClass";
$fqn3 = "Doctrine\ORM\Mapping";
```
This is equivalent to the return value from get_class()

### class name
The class Name is the name of the class without its namespace. Its distinct from an FQN.

```php
$fqn1 = "Webforge\Code\Test\Base";
$className1 = 'Base';

$fqn2 = "stdClass";
$className2 = 'stdClass';

$fqn3 = "Doctrine\ORM\Mapping";
$className2 = 'Mapping';
```

### Package
the term is used as used in composer. A package is a collection of class files and scripts or binaries that represent a common context. A Package can be a kind of library, or application, or something else. On github one repository would be considered as a package.