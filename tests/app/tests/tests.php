<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace tests;

use bravedave\dvc\logger;
use dvc, application;
use strings;

class tests extends dvc\service {

  public static function testmail() {

    $app = new self(application::startDir());
    $app->_testmail();
  }

  protected function _testmail() {
    // logger::info(sprintf('<%s> %s', \config::$MAILDSN, __METHOD__));
    $to = '';
    if (isset($_SERVER['argv'])) {

      foreach ($_SERVER['argv'] as $arg) {

        if (preg_match('@^to=@i', $arg)) {
          $to = preg_replace('@^to=@i', '', $arg);
          break;
        }
      }
    }

    if (strings::isEmail($to)) {

      $email = dvc\sendmail::email()
        ->to('david@brayworth.com.au')
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject('Sent using Symfony Mailer!')
        ->text('Sending emails is fun again!')
        ->html('<h1>Sending emails is fun again!</h1>');

      dvc\sendmail::send($email);
    } else {

      logger::info(sprintf('<specify a valid to address> %s', __METHOD__));
      logger::info(sprintf('<composer send-testmail to=someone@example.com> %s', __METHOD__));
    }
  }
}
