<?php

namespace Webforge\Console;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

class InteractionHelperTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\InteractionHelper';
    parent::setUp();

    $this->dialogHelper = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
    $this->output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');

    $this->interaction = new InteractionHelper($this->dialogHelper, $this->output);
  }

  public function testAskDelegatesOutputTheQuestionAndTheDefaultValueToTheDialog() {
    $question = 'How is your class called?';
    $default = NULL;

    $this->dialogHelper->expects($this->once())->method('ask')
      ->with($this->output, $question.' ', $default)
      ->will($this->returnValue($answer = 'answer'));

    $this->assertEquals(
      $answer,
      $this->interaction->ask($question)
    );
  }

  public function testAskDefaultAddsTheDefaultToTheQuestionFormatted() {
    $question = 'How is your class called?';
    $default = 'NiceClass';

    $this->dialogHelper->expects($this->once())->method('ask')
      ->with($this->output, $question.' (default NiceClass): ', $default)
      ->will($this->returnValue($answer = 'answer'));

    $this->assertEquals(
      $answer,
      $this->interaction->askDefault($question, $default)
    );
  }

  public function testAskConfirmationFormatsTheQuestion() {
    $question = 'Do you want to continue?';

    $this->dialogHelper->expects($this->once())->method('askConfirmation')
      ->with($this->output, $question.' ')
      ->will($this->returnValue($answer = 'yes'));

    $this->assertEquals(
      $answer,
      $this->interaction->confirm($question)
    );
  }

  public function testAskAndValidatePassesTheValidator() {
    $question = 'Give some valid input:';
    $attempts = FALSE;
    $validator = function ($typed) { 
      if ($typed === 'banane') {
        throw new \RuntimeException('banane is not valid input');
      }

      return $typed;
    };

    $this->dialogHelper->expects($this->once())->method('askAndValidate')
      ->with($this->output, $question.' ', $validator, $attempts)
      ->will($this->returnValue($answer = 'okay'));

    $this->assertEquals(
      $answer,
      $this->interaction->askAndValidate($question, $validator, $attempts)
    );
  }
}
