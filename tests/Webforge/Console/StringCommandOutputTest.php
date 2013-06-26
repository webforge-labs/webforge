<?php

namespace Webforge\Console;

class StringCommandOutputTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\StringCommandOutput';
    parent::setUp();

    $this->output = new StringCommandOutput();
  }

  public function testImplementsRightInterface() {
    $this->assertInstanceOf('Webforge\Console\CommandOutput', $this->output);
  }

  public function testMessagesWillBeAppendedAsNewLines() {
    $this->output->ok('found requirements');
    $this->output->ok('done');

    $this->assertEquals(
      "ok: found requirements\n".
      "ok: done\n",
      $this->output->toString()
    );
  }

}
