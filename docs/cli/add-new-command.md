# Add a new Command to the Webforge CLI

  
  1. create a new Class: `Webforge\Framework\CLI\<yourCommand>` extends `Webforge\Framework\CLI\ContainerCommand`
    - you can overwrite setup() to instantiate some helper classes, or init
    - implement `getDescription` which returns a string describing the command
    - implement `defineArguments(array $api)`. start with `extract($api)` and then use the CommandsIncluder - API to create arguments and options
    - implement `public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact)` to parse the arguments from input into usable arguments for your class
    - call your own execute function with the parsed arguments (for example parse a dir string into an actual Webforge\Common\System\Dir)
    - use `$this->interact` (Webforge\Console\CommandInteraction) and `$this->output` (Webforge\Console\CommandOutput) and `$this->system` (Webforge\Common\System\ExecutionSystem) to ask questions, display messages or execute commands.
    - use `$this->container` to get access to the webforge container (to create classes, etc ...)
  1. create a new Test for the class extending Webforge\Framework\CLI\CommandTestCase
    - refer to the section below for specific tests
  2. add your command to the `Webforge\Framework\CLI\Application`


## testing your command

All mocks are mockery/mockery mocks. $this->output, $this->input, $this->interactionHelper (use helper methods), and $this->system are pre-mocked for you. You can use the snippet below to start with your command test.
It's easy to test for confirmations, etc:


```php
 $this->expectConfirm()
  ->with('/Do you want to do it/i', $default = TRUE)
  ->andReturn($answer);
```

This expects the mock to get a question with an regexp matching the question string. The suggested default should be 'yes' and the actual result returned to the command will be `$answer`.

```php
$this->expectQuestion()
  ->with('/namespace/i', 'Acme\Superblog')
  ->andReturn('ACME\SuperBlog');
```

This is the same with a normal question. The word "namespace" matches the question here. Think about some reformulations of your question while testing. It would be nicer if we would define language strings here and compare them.
Test system commands like this:

```php
 $this->system
   ->shouldReceive('passthru')
   ->ordered('execs')
   ->once()
   ->with(m::on(function ($commandline) {
      // do something the system would do
   });
```

### run

```php
protected function execute() {
  $this->initOI($this->myCommand);

  return $this->myCommand->execute($this->parsedArgument1);
}
```

### template
```php
<?php

namespace Webforge\Framework\CLI;

class {{MyCommand}}Test extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\CLI\\{{MyCommand}}';
    parent::setUp();

    $this->cmd = new {{MyCommand}}($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testThatCommandRuns() {
    $this->expectCustomQuestion1();
    $this->expectCustomConfirmation(TRUE);

    $this->execute();
  }

  protected function expectCustomConfirmation($answer) {
    $this->expectConfirm()
    ->with('/continue/i', $default = TRUE)
    ->andReturn($answer);
  }

  protected function expectCustomQuestion1() {
    $this->expectQuestion()
    ->with('/fqn of namespace/i', $default = 'Acme\Superblog')
    ->andReturn('ACME\SuperBlog');
  }

  protected function execute() {
    $this->initOI($this->cmd);

    return $this->cmd->execute($this->parsedArgument1);
  }
}
```