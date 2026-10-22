<?php
require __DIR__ . '/../../../vendor/autoload.php';

use bravedave\dvc\{breadcrumb, url};
use bravedave\dvc\esse\render6;

class currentUser {
  public static function option($name) { return ''; }
}

class theme {
  public static function secondary() { return ''; }
}

url::$URL = '/';
$render = function (array $options): string {
  ob_start();
  (new render6)($options);
  return ob_get_clean();
};

$html = $render([
  'title' => '<Title>',
  'bodyClass' => 'demo" onload="bad',
  'navbar' => fn() => print '<nav>Navigation</nav>',
  'aside' => fn() => print 'Aside content',
  'main' => fn() => print 'Main content',
  'footer' => fn() => print '<footer>Footer content</footer>',
  'breadcrumb' => [new breadcrumb('<Crumb>', '/?a=1&b=2')],
  'css' => ['/?style="test"'],
  'scripts' => ['/?script="test"'],
]);

$checks = [
  'document closes once' => substr_count($html, '</html>') === 1 && str_ends_with(trim($html), '</html>'),
  'title escaped' => str_contains($html, '<title>&lt;Title&gt;</title>'),
  'breadcrumb escaped' => str_contains($html, '&lt;Crumb&gt;') && str_contains($html, '/?a=1&amp;b=2'),
  'body class escaped' => !str_contains($html, ' onload="bad'),
  'asset shorthand escaped' => str_contains($html, '&quot;test&quot;'),
  'slot order' => strpos($html, 'Navigation') < strpos($html, 'Aside content') && strpos($html, 'Aside content') < strpos($html, 'Main content') && strpos($html, 'Main content') < strpos($html, 'Footer content'),
  'jquery included' => str_contains($html, 'assets/jquery/4'),
  'bootstrap css included' => str_contains($html, 'assets/bootstrap/css/6'),
  'icons included' => str_contains($html, 'assets/bootstrap/icons'),
  'versioned esse stylesheet included' => substr_count($html, 'href="/assets/esse/6"') === 1,
  'render6 stylesheet registered' => in_array(realpath(__DIR__ . '/../../../src/bravedave/dvc/esse/render6.css'), \bravedave\dvc\cssmin::esseFiles(6), true),
  'legacy bundle excludes render6' => !in_array(realpath(__DIR__ . '/../../../src/bravedave/dvc/esse/render6.css'), \bravedave\dvc\cssmin::esseFiles(), true),
  'render6 bundle excludes legacy styles' => count(\bravedave\dvc\cssmin::esseFiles(6)) === 2 && !in_array(realpath(__DIR__ . '/../../../src/bravedave/dvc/esse/esse.css'), \bravedave\dvc\cssmin::esseFiles(6), true),
  'module bootstrap included' => str_contains($html, '<script type="module" src="/assets/bootstrap/js/6'),
  'no legacy scripts' => !str_contains($html, 'assets/brayworth/') && !str_contains($html, '_brayworth_'),
];

preg_match('/<script type="importmap">(.*?)<\/script>/s', $html, $matches);
$imports = json_decode($matches[1] ?? '{}', true);
$checks['bootstrap import map'] = str_starts_with($imports['imports']['@bootstrap'] ?? '', '/assets/bootstrap/js/6?v=');

$empty = $render([]);
$checks['optional sections omitted'] = !str_contains($empty, '<aside') && !str_contains($empty, '<footer') && !str_contains($empty, '<nav');
$checks['empty document complete'] = str_contains($empty, '<main') && str_ends_with(trim($empty), '</html>');

$failed = array_keys(array_filter($checks, fn($passed) => !$passed));
if ($failed) {
  fwrite(STDERR, implode("\n", $failed) . "\n");
  exit(1);
}
printf("Render6: %d checks passed\n", count($checks));