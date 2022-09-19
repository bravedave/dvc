<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * https://symfony.com/doc/current/mailer.html
*/

namespace dvc;

use Symfony\Component\Mime\{
  Address,
  Email
};
use Symfony\Component\Mailer\{
  Transport,
  Mailer
};

abstract class sendmail {
  static function address(string $email, string $name) {
    return new Address($email, $name);
  }

  static function email($forum = false): Email {
    $email = new Email();

    $email->getHeaders()
      ->addTextHeader('X-Mailer', \config::$MAILER);

    $email->from(new Address(\config::$SUPPORT_EMAIL, \config::$SUPPORT_NAME));

    return $email;
  }

  static function mailer() {
    if (\config::$MAILDSN) {
      $transport = Transport::fromDsn(\config::$MAILDSN);
      return new Mailer($transport);
    } else {
      \sys::logger(sprintf('<%s> %s', 'please configure your config::$MAILDSN', __METHOD__));
    }
  }

  static function send(Email $email) {
    if ($mailer = self::mailer()) {

      $mailer->send($email);
    }
  }
}
