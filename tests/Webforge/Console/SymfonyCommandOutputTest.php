<?php

namespace Webforge\Console;

use Symfony\Component\Console\Output\StreamOutput;

class SymfonyCommandOutputTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\SymfonyCommandOutput';
    parent::setUp();

    // maybe mock it?
    $this->output = new SymfonyCommandOutput($this->stream = new StreamOutput(fopen('php://memory', 'r+')));
  }

  public function testHasOKMethod() {
    $string = $this->output->ok('done and finished.');

    rewind($this->stream->getStream());

    $this->assertContains('done and finished.', stream_get_contents($this->stream->getStream()));
  }
}
