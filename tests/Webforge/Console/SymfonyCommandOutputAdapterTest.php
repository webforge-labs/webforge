<?php

namespace Webforge\Console;

use Symfony\Component\Console\Output\StreamOutput;

class SymfonyCommandAdapterOutputTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\SymfonyCommandOutputAdapter';
    parent::setUp();

    $this->output = new SymfonyCommandOutputAdapter($this->stream = new StreamOutput(fopen('php://memory', 'r+')));
  }

  public function testHasOKMethod() {
    $string = $this->output->ok('done and finished.');

    rewind($this->stream->getStream());

    $this->assertContains('done and finished.', stream_get_contents($this->stream->getStream()));
  }
}
