<?php
/*
 * Copyright (c) 2025 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
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
 * bravedave\dvc\normaliseText
 *
 * Normalises Unicode typographic characters to their plain ASCII equivalents.
 * Use before inserting user-supplied text into PDF templates, plain-text
 * outputs, or any renderer that cannot reliably handle Unicode typography
 * (e.g. dompdf with Latin-1 fonts, SMS gateways, legacy email clients).
 *
 * Handles:
 *   - Smart single quotes / apostrophes  U+2018 U+2019 U+201A  →  '
 *   - Smart double quotes                U+201C U+201D U+201E  →  "
 *   - En dash                            U+2013                →  -
 *   - Em dash                            U+2014                →  --
 *   - Horizontal ellipsis               U+2026                →  ...
 *   - Non-breaking space                 U+00A0                →  (space)
 *   - Soft hyphen                        U+00AD                →  (removed)
 *
 * @param  string|null $value  Raw input text
 * @return string              Text safe for ASCII/Latin-1 renderers
 */
function normaliseText(string|null $value): string {

  if (!$value) return '';

  $search = [
    // Smart single quotes and apostrophe (U+2018, U+2019, U+201A)
    sprintf('@(%s|%s|%s)@u', "\u{2018}", "\u{2019}", "\u{201A}"),
    // Smart double quotes (U+201C, U+201D, U+201E)
    sprintf('@(%s|%s|%s)@u', "\u{201C}", "\u{201D}", "\u{201E}"),
    // Em dash (U+2014) — before en dash so it gets '--' not '- '
    sprintf('@%s@u', "\u{2014}"),
    // En dash (U+2013)
    sprintf('@%s@u', "\u{2013}"),
    // Horizontal ellipsis (U+2026)
    sprintf('@%s@u', "\u{2026}"),
    // Non-breaking space (U+00A0)
    sprintf('@%s@u', "\u{00A0}"),
    // Soft hyphen (U+00AD) — invisible, remove entirely
    sprintf('@%s@u', "\u{00AD}"),
  ];

  $replace = ["'", '"', '--', '-', '...', ' ', ''];

  return preg_replace($search, $replace, $value);
}

/**
 * Alias of normaliseText() for those who prefer American spelling.
 */
function normalizeText(string|null $value): string {
  return normaliseText($value);
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
        'id_prefix' => 'content',                    // Optional: Prefix for the generated IDs
        'insert' => 'before',                 // Optional: Insert the permalink before or after the heading
        'symbol' => ''                        // Optional: Symbol for the permalink (empty for no symbol)
      ];
    }

    // Create environment with GitHub Flavored Markdown

    $_env = new Environment($environment);
    $_env->addExtension(new CommonMarkCoreExtension());
    $_env->addExtension(new GithubFlavoredMarkdownExtension());

    if (isset($environment['heading_permalink'])) {

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
