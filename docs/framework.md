# the webforge framework

As you might noticed in the previous chapters, webforge is a command line interface application. But webforge can help you even more as installing stuff for your packages and create tests and classes.

## the big picture

### backend

When we talk about the webforge framework we generally mean the [webforge/webforge](http://packagist.org/packages/webforge/webforge) repository. The [webforge/devtool](http://packagist.org/packages/webforge/devtool) installs the framework as a CLI application and makes it easy updatable. But you can use features from the framework classes in your application as well. For example you can upgrade your package to a project to

  - add a web interface / application
  - add an admin interface / application
  - deploy the package on a (web)-server
  - have stage modi like: staging, development and production for your package
  - manage urls and get routing components
  - and much more

Depending on the type of your project you might want to use serveral components from webforge components. You can find them all in the organization from [webforge-labs on github](http://github.com/webforge-labs). Components are independent from each other and you can cherry pick your components for your specific needs with composer.
Just do something like this:

```
composer require webforge-labs/the-component
```
You can add `--dev` if you just need this component for your tests. Composer will pull everything in for you and you just have to read the manual of the component to get started.

### frontend

Once your backend is ready you might want to add a frontend as well. Here are some interesting starting points for you:

[Webforge as a company](http://www.ps-webforge.com) provides some libraries that will build your frontend:
  
  - [psc-cms](https://github.com/webforge-labs/psc-cms)
  - [psc-cms client](https://github.com/webforge-labs/psc-cms-js)
  - [shimney-js](https://github.com/webforge-labs/shimney-js)
  - [cojoko](https://github.com/webforge-labs/cojoko)
  - [minimock](https://github.com/webforge-labs/minimock)
