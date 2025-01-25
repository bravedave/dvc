<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

use League\CommonMark;

class MarkdownConverter extends CommonMark\MarkdownConverter {

  /**
   * Create a new Markdown converter pre-configured for GFM
   *
   * @param array<string, mixed> $config
   */
  public function __construct(array $config = []) {

    $environment = new CommonMark\Environment\Environment($config);
    $environment->addExtension(new CommonMark\Extension\CommonMark\CommonMarkCoreExtension);
    $environment->addExtension(new CommonMark\Extension\GithubFlavoredMarkdownExtension);

    // Add the extension
    $environment->addExtension(new CommonMark\Extension\Footnote\FootnoteExtension);

    parent::__construct($environment);
  }
}
