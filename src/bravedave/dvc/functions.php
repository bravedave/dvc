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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

/**
 * bravedave\dvc\esc
 * @return string HTML-safe
 */
function esc(string|null $v): string {

  if ($v) return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8', false);
  // if ($v) return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
  return '';
}

/**
 * Convert text to HTML using GitHub Flavored Markdown
 * 
 * @param string|null $inText The input text to convert
 * @param int $maxrows Maximum number of rows to return (0 = no limit)
 * @return string HTML output
 */
function text2html(string|null $inText, int $maxrows = 0, array $environment = [], bool $heading_permalink = true): string {

  if (empty($inText)) return '';

  try {

    $environment = array_merge([
      'html_input' => 'strip',
      'allow_unsafe_links' => false,
      'max_nesting_level' => 100,
      'renderer' => [
        'soft_break' => "<br>\n",
      ],
    ], $environment);

    if ($heading_permalink && !isset($environment['heading_permalink'])) {
      $environment['heading_permalink'] = [
        'html_class' => 'heading-permalink',  // Optional: Add a CSS class
        'id_prefix' => 'content',             // Optional: Add a prefix to IDs
        'insert' => 'before',                 // Optional: Insert the permalink before or after the heading
        'symbol' => ''                        // Optional: Insert the permalink before or after the heading
      ];
    }

    // Create environment with GitHub Flavored Markdown

    $_env = new Environment($environment);
    $_env->addExtension(new CommonMarkCoreExtension());
    $_env->addExtension(new GithubFlavoredMarkdownExtension());

    if (!isset($environment['heading_permalink'])) {

      $_env->addExtension(new HeadingPermalinkExtension);
    }

    // Create converter
    $converter = new MarkdownConverter($_env);

    // If maxrows is specified, limit the input text
    if ($maxrows > 0) {
      $lines = explode("\n", $inText);
      if (count($lines) > $maxrows) {
        $lines = array_slice($lines, 0, $maxrows);
        $inText = implode("\n", $lines);
      }
    }

    // Convert markdown to HTML
    $html = $converter->convert($inText)->getContent();

    return $html;
  } catch (\Exception $e) {
    // Fallback to basic HTML conversion if markdown parsing fails
    $text = esc($inText);

    // If maxrows is specified, limit the output
    if ($maxrows > 0) {
      $lines = explode("\n", $text);
      if (count($lines) > $maxrows) {
        $lines = array_slice($lines, 0, $maxrows);
        $text = implode("\n", $lines);
      }
    }

    // Convert line breaks to <br> tags
    return nl2br($text);
  }
}
