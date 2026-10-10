<?php
/*
 * Assertions for bravedave\dvc\view
 * run from the repository root: php tests/dvc/ViewTest.php
*/

require __DIR__ . '/../../vendor/autoload.php';

use bravedave\dvc\view;

final class ViewTest {

  private int $passed = 0;
  private array $failed = [];
  private string $base;
  private string $views;

  function __construct() {

    $this->base = sys_get_temp_dir() . '/dvc-view-test-' . bin2hex(random_bytes(4));
    $this->views = $this->base . '/views';

    mkdir($this->views, 0777, true);
    mkdir($this->base . '/views-evil', 0777, true);

    file_put_contents($this->views . '/hello.php', '<?= "hello " . $who ?>');
    file_put_contents($this->views . '/notes.md', "# Title\n\nbody text\n");
    file_put_contents($this->views . '/both.php', 'php wins');
    file_put_contents($this->views . '/both.md', '# md wins');
    mkdir($this->views . '/dirview.php', 0777, true);
    file_put_contents($this->base . '/views-evil/a.php', 'evil');
    symlink('/etc/passwd', $this->views . '/link.php');
  }

  private function render(callable $fn): array {

    ob_start();
    $result = $fn();
    $output = ob_get_clean();

    return [$result, $output];
  }

  private function assertTrue(bool $condition, string $message): void {

    if ($condition) {
      $this->passed++;
    } else {
      $this->failed[] = $message;
    }
  }

  private function view(array $data = ['who' => 'world']): view {

    return new view($data, [$this->views]);
  }

  function testRelativePhpIsRenderedWithData(): void {

    [$ok, $out] = $this->render(fn() => $this->view()->load('hello'));

    $this->assertTrue($ok === true, 'relative .php: load() should return true');
    $this->assertTrue($out === 'hello world', 'relative .php: data should be extracted, got: ' . $out);
  }

  function testMarkdownIsWrapped(): void {

    [$ok, $out] = $this->render(fn() => $this->view()->load('notes'));

    $this->assertTrue($ok === true, 'relative .md: load() should return true');
    $this->assertTrue(str_starts_with($out, '<div class="markdown-body">'), 'relative .md: should open the markdown-body wrapper');
    $this->assertTrue(str_contains($out, '<h1'), 'relative .md: should render the heading');
    $this->assertTrue(str_ends_with($out, '</div>'), 'relative .md: should close the markdown-body wrapper');
  }

  function testPhpIsPreferredOverMarkdown(): void {

    [, $out] = $this->render(fn() => $this->view()->load('both'));

    $this->assertTrue(str_contains($out, 'php wins'), 'both.php and both.md: .php should be chosen');
    $this->assertTrue(!str_contains($out, 'md wins'), 'both.php and both.md: .md should not be chosen');
  }

  function testWrapClassesAreApplied(): void {

    $v = $this->view();
    $v->wrap = ['card', 'p-3'];

    [, $out] = $this->render(fn() => $v->load('hello'));

    $this->assertTrue(str_starts_with($out, '<div class="card"><div class="p-3">hello world'), 'wrap: classes should open in order');
    $this->assertTrue(substr_count($out, '<div') === substr_count($out, '</div>'), 'wrap: open and close div counts should match');
  }

  function testTraversalIsRefused(): void {

    [$a] = $this->render(fn() => $this->view()->load('../views/hello'));
    [$b] = $this->render(fn() => $this->view()->load('hello/../../views/hello'));

    $this->assertTrue($a === false, 'traversal: "../" should fail');
    $this->assertTrue($b === false, 'traversal: embedded "../" should fail');
  }

  function testNullByteIsRefused(): void {

    [$ok] = $this->render(fn() => $this->view()->load("hello\0"));

    $this->assertTrue($ok === false, 'null byte: name should fail');
  }

  function testAbsoluteSystemFileFails(): void {

    [$ok, $out] = $this->render(fn() => $this->view()->load('/etc/passwd'));

    $this->assertTrue($ok === false, 'absolute /etc/passwd: load() must fail');
    $this->assertTrue(!str_contains($out, ':0:0:'), 'absolute /etc/passwd: file contents must not be printed');
  }

  function testAbsoluteFileInsideRootLoads(): void {

    [$ok, $out] = $this->render(fn() => $this->view()->load($this->views . '/hello'));

    $this->assertTrue($ok === true, 'absolute path inside a root: load() should return true');
    $this->assertTrue($out === 'hello world', 'absolute path inside a root: should render, got: ' . $out);
  }

  function testSiblingPrefixDirectoryIsRefused(): void {

    [$ok] = $this->render(fn() => $this->view()->load($this->base . '/views-evil/a'));

    $this->assertTrue($ok === false, 'sibling prefix: views-evil must not match the views root');
  }

  function testSymlinkEscapeIsRefused(): void {

    [$ok, $out] = $this->render(fn() => $this->view()->load('link'));

    $this->assertTrue($ok === false, 'symlink to /etc/passwd: load() should fail');
    $this->assertTrue(!str_contains($out, ':0:0:'), 'symlink to /etc/passwd: file contents must not be printed');
  }

  function testDirectoryIsNotAView(): void {

    [$ok] = $this->render(fn() => $this->view()->load('dirview'));

    $this->assertTrue($ok === false, 'directory named dirview.php: should not be a view');
  }

  function testMissingViewReportsNotFound(): void {

    [$ok, $out] = $this->render(fn() => $this->view()->load('does-not-exist'));

    $this->assertTrue($ok === false, 'missing view: load() should return false');
    $this->assertTrue(str_contains($out, 'not found'), 'missing view: should report not found');
  }

  function testInvokeLoadsLikeLoad(): void {

    $v = $this->view(['who' => 'invoke']);
    [$ok, $out] = $this->render(fn() => $v('hello'));

    $this->assertTrue($ok === true, 'invoke: should return true for an existing view');
    $this->assertTrue($out === 'hello invoke', 'invoke: should render like load(), got: ' . $out);
  }

  private function cleanup(string $path): void {

    if (is_link($path) || is_file($path)) {
      unlink($path);
    } elseif (is_dir($path)) {
      foreach (scandir($path) as $entry) {
        if ($entry !== '.' && $entry !== '..') $this->cleanup($path . '/' . $entry);
      }
      rmdir($path);
    }
  }

  function run(): int {

    foreach (get_class_methods($this) as $method) {

      if (!str_starts_with($method, 'test')) continue;

      try {
        $this->$method();
      } catch (\Throwable $e) {
        $this->failed[] = sprintf('%s threw: %s', $method, $e->getMessage());
      }
    }

    $this->cleanup($this->base);

    printf("%d passed, %d failed\n", $this->passed, count($this->failed));
    foreach ($this->failed as $message) printf("FAIL: %s\n", $message);

    return $this->failed ? 1 : 0;
  }
}

exit((new ViewTest)->run());
