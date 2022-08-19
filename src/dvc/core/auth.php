<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\core;

use dvc\html;

abstract class auth {
  static function button() {
    if (\auth::GoogleAuthEnabled()) {
      if (\currentUser::valid()) {
        return (sprintf(
          '<a href="%s"><img alt="logout" src="%s" /><img alt="avatar" class="user-avatar" title="%s" src="%s" /><img alt="logout" src="%s" /></a>',
          \strings::url('auth/logout'),
          \strings::url('images/logout-left9x50.png'),
          \currentUser::user()->name,
          \currentUser::avatar(),
          \strings::url('images/logout-63x50.png')
        ));
      } else {
        return (sprintf(
          '<a href="#dlgLogon" data-toggle="modal"><img alt="logon with google" src="%s" /></a>',
          \strings::url('images/gfb-signin-246x54.png')
        ));
      }
    }

    return ('');
  }

  static function dialog() {
    $top = new html\div;
    $top->attributes([
      'class' => 'modal fade',
      'id' => 'dlgLogon',
      'tabindex' => '-1',
      'role' => 'dialog',
      'aria-labelledby' => 'myModalLabel',
      'aria-hidden' => 'true'

    ]);

    $div = $top->append('div', null, [
      'class' => 'modal-dialog modal-sm',
      'id' => 'dlgLogonDialog'

    ]);

    $content = $div->append('div', null, [
      'class' => 'modal-content'

    ]);

    $header = $content->append('div', null, [
      'class' => 'modal-header'
    ]);

    $button = $header->append('button', null, [
      'type' => 'button',
      'class' => 'close',
      'data-dismiss' => 'modal',
      'aria-label' => 'Close'
    ]);

    $button->append('span', '&times;', ['aria-hidden' => 'true']);

    $header->append('h4', 'Sign in to you account using', [
      'class' => 'modal-title',
      'id' => 'dlgLogonTitle'
    ]);

    $body = $content->append('div', null, [
      'class' => 'modal-body',
      'id' => 'dlgLogonBody'
    ]);

    $a = new html\a(\strings::url(\currentUser::valid() ? 'auth/logout' : 'auth/request'), '');
    if (\currentUser::valid()) {
      $a->appendChild(new html\img(\strings::url('images/logout-left9x50.png'), 'logout'));
      $img = new html\img(\currentUser::avatar(), 'avatar');
      $img->attributes(['title' => \currentUser::DisplayName()]);
      $a->appendChild($img);
      $a->appendChild(new html\img(\strings::url('images/logout-63x50.png'), 'logout'));
    } else {
      $a->appendChild(new html\img(\strings::url('images/g-signin-266x54.png'), 'logon with google'));
    }

    $body->appendChild($a);

    $a = new html\a(\strings::url(\currentUser::valid() ? 'fbauth/logout' : 'fbauth/request'), '');
    if (\currentUser::valid()) {
      $a->appendChild(new html\img(\strings::url('images/logout-left9x50.png'), 'logout'));
      $img = new html\img(\currentUser::avatar(), 'avatar');
      $img->attributes(['title' => \currentUser::DisplayName()]);
      $a->appendChild($img);
      $a->appendChild(new html\img(\strings::url('images/logout-63x50.png'), 'logout'));
    } else {
      $a->appendChild(new html\img(\strings::url('images/fb-signin-266x54.png'), 'logon with facebook'));
    }

    $body->appendChild($a);

    $a = new html\a(\strings::url(\currentUser::valid() ? 'odauth/logout' : 'odauth/request'), '');
    if (currentUser::valid()) {
      $a->appendChild(new html\img(\strings::url('images/logout-left9x50.png'), 'logout'));
      $img = new html\img(currentUser::avatar(), 'avatar');
      $img->attributes(array('title' => currentUser::DisplayName()));
      $a->appendChild($img);
      $a->appendChild(new html\img(\strings::url('images/logout-63x50.png'), 'logout'));
    } else {
      $a->appendChild(new html\img(\strings::url('images/ms-signin-266x54.png'), 'logon with microsoft'));
    }

    $body->appendChild($a);
  }

  static function FacebookAuthEnabled() {
    if (is_null(\config::$facebook_oauth2_client_id) || is_null(\config::$facebook_oauth2_secret) || is_null(\config::$facebook_oauth2_redirect))
      return (false);

    return (true);
  }

  static function GoogleAuthEnabled() {
    if (is_null(\config::$oauth2_client_id) || is_null(\config::$oauth2_secret) || is_null(\config::$oauth2_redirect))
      return (false);

    return (true);
  }

  static function ImapAuthEnabled() {
    if (is_null(\config::$IMAP_AUTH_SERVER)) {
      return (false);
    }

    return (true);
  }

  static function ImapTest(string $u, string $p): bool {
    $debug = false;
    // $debug = true;

    $port = '143';
    $secure = 'tls';
    $inbox = 'Inbox';
    $server = \config::$IMAP_AUTH_SERVER;
    if (preg_match('@^ssl://@', $server)) {
      $port = '993';
      $secure = 'ssl';
      $server = preg_replace('@^ssl://@', '', $server);
    }

    $server = sprintf(
      '{%s:%s/%s}%s',
      $server,
      $port,
      $secure,
      $inbox

    );

    try {
      if ($stream = imap_open($server, $u, $p, OP_HALFOPEN, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI'])) {
        imap_close($stream);
        return true;
      }
    } catch (\Throwable $th) {
      if ($debug) \sys::logger(sprintf('<fail on %s> <%s:%s> %s', $server, $u, $p, __METHOD__));
    }

    return false;
  }
}
