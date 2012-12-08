# Webforge Code Generator

## TODO
  
  - class Writing: Syntax is often wrong (wrong whitespacing)
  - class Writing: is not pretty

## GClass
The GClass (aka GenerateClass) is the core of the Code Generator. It encapsulates a whole class with:

  - methods (GMethod)
  - properties (GProperty)
  - constants (GConstant)
  - interfaces (GClass)
  - parent(s) (GClass)
  - class-DocBlock (Psc\Code\Generate\DocBlock)

GClasses can be read from sources or can be created from scratch. Classes can be written to a desired programming language with the class writer.
The API of the GClass tries to avoid the need for a classbuilder, but mostly the complexity of some operations is often underestimated. Most Operations from Webforge work with the GClass, where one important difference should be kept in mind: GClasses can be used to create classes and to modify classes.
The GClass is *elevated*, when the underlying source was read by a parser / reader. When the GClass is created from scratch, one might think that class reading is not necessary, but it is. Parents of GClasses have to be always elevated, because sometimes its relevant that the parent class has already defined a method, which should be created in the child class (to avoid conflicts of the signature, e.g.).
So care should be taken for classes that modify GClasses, always remember, that you might have to check if class already exists in the context and you might have to elevate one or more parent classes.
This leads to some complications, when you try to modify classes that have parents which cannot be elevated.
Remember that the GClass should not be tied to a specific programming language, allthough it abstracts only object oriented classes.

### Traversing / Querying
You can get Methods / Properties / Constants etc always by its name. You can get Interfaces by its FQN. Always use the `getSingular()`-Methods for this. For other methods like `getMethods` or `getProperties` there is one limitation: Those function always return the explicit items from the GClass. For example `getMethods()` will not return the methods from the full class hierarchy, and only the created or elevated ones. If you want to have all Methods from Hierarchy use the `getAlLMethods`, it allows you to pick certain methods of the GClass-Hierarchy.
This limitation affects the way `hasSingular` works, as well. `hasMethod` will return only true if the method is in the collection returned by `getMethods()`.

### Modification
Its easy to create methods / properties / constants in the GClass. Use the create* methods for this. They are a shortcoming for constructring a new method or property and adding it to the class. The underlying GObjectCollection lets you sort the properties, methods, etc. Sorting is only relevant when the class is written back to source.

### Elevation
GClasses are elevated with the [nikic/PHP_Parser](https://github.com/nikic/PHP_Parser). The Code is parsed and then transformed into GClasses. Unfortunately the PHP-Reflection provides not enough information for reading classes (missing Comments, no indenting information, etc). This was the main point of failure in Psc-CMS.