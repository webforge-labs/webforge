# Testing Setup

Cuked Zombie (in browser.options) sets:  'X-Environment-In-Tests' to 'from-zombie' on every request.
Symfony receives this in it's front controller (index.php) with: 
```
if ($inTests = $request->headers->has('X-Environment-In-Tests')) {
  $container->enableTestEnvironment();
}
```

If phpunit is run you should add:
```
    <php>
      <const name="phpunit" value="1"/>
    </php>
```
the project-stack BootContainer checks for this and does an enableTestEnvironment() as well.

**Note**: the project-stack fixture parts cli - command will ALWAYS use the database from the environment. So be carefull - it uses the DEFAULT doctrine connection for this.
