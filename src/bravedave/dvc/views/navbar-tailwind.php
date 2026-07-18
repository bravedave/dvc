<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * compatibility : tailwind
*/

namespace bravedave\dvc;

use application as app;

$title = $title ?? $this->title;
$aside = ($aside ?? true);
$pageUrl = $pageUrl ?? null;
$menu = [];

$menuJson = app::app()->getRootPath() . '/menu.json';
if (file_exists($menuJson)) {

  $menu = (array)json_decode(file_get_contents($menuJson), true);
}

$uid = strings::rand();
$mobileMenu = strings::rand();
$asideButton = strings::rand();
?>

<nav id="<?= $uid ?>" class="w-full border-b border-blue-700 bg-blue-600" role="navigation">
  <div class="mx-auto flex w-full max-w-screen-2xl flex-wrap items-center gap-2 px-3 py-2 text-white md:flex-nowrap md:px-4">

  <?php if ($aside) { ?>
    <button
      type="button"
      id="<?= $asideButton ?>"
      class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/40 text-white hover:bg-blue-500 md:hidden"
      aria-label="Toggle aside">
      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M12 5h.01M12 12h.01M12 19h.01"></path>
      </svg>
    </button>
  <?php } ?>

  <div class="min-w-0 flex-1">

    <?php if ($menu) { ?>
      <details class="relative inline-block" id="<?= strings::rand() ?>">
        <summary class="list-none cursor-pointer rounded-md bg-blue-800 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
          <?= esc($title) ?>
        </summary>

        <ul class="absolute left-0 z-50 mt-2 w-64 rounded-md border border-slate-200 bg-white py-1 shadow-lg">
          <?php foreach ($menu as $item) { ?>
            <li>
              <a
                class="block px-3 py-2 text-sm text-slate-700 hover:bg-slate-100"
                href="<?= strings::url($item['url']) ?>">
                <?= esc($item['title']) ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </details>
    <?php } elseif ($pageUrl) {

      printf('<a class="truncate text-base font-semibold text-white hover:text-blue-100" href="%s">%s</a>', $pageUrl, esc($title));
    } else {

      printf('<div class="truncate text-base font-semibold text-white">%s</div>', esc($title));
    } ?>
  </div>

  <button
    type="button"
    class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/40 text-white hover:bg-blue-500 md:hidden"
    aria-controls="<?= $mobileMenu ?>"
    aria-expanded="false"
    data-role="mobile-nav-toggle"
    aria-label="Toggle navigation">
    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <path d="M3 6h18M3 12h18M3 18h18"></path>
    </svg>
  </button>

  <div id="<?= $mobileMenu ?>" class="hidden w-full md:ml-auto md:block md:w-auto">
    <ul class="mt-2 flex flex-col gap-1 md:mt-0 md:flex-row md:items-center md:gap-2">
      <li>
        <a class="block rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-blue-500" href="<?= strings::url() ?>">Home</a>
      </li>
      <li>
        <a class="block rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-blue-500" href="<?= strings::url('docs/') ?>">docs</a>
      </li>
      <li>
        <a class="block rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-blue-500" href="https://github.com/bravedave/">GitHub</a>
      </li>
    </ul>
  </div>
  </div>
</nav>

<?php if ($aside) { ?>
  <style>
    @media (max-width: 767px) {
      body:not(.show-aside) aside[data-role='content-secondary'] {
        display: none !important;
      }

      body.show-aside main[data-role='content-primary'] {
        display: none !important;
      }
    }
  </style>
<?php } ?>

<script>
  (() => {
    const root = document.getElementById('<?= $uid ?>');
    if (!root) return;

    const menuToggle = root.querySelector('[data-role="mobile-nav-toggle"]');
    const mobileMenu = document.getElementById('<?= $mobileMenu ?>');

    if (menuToggle && mobileMenu) {
      menuToggle.addEventListener('click', () => {
        const isHidden = mobileMenu.classList.toggle('hidden');
        menuToggle.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
      });
    }

    <?php if ($aside) { ?>
      const asideBtn = document.getElementById('<?= $asideButton ?>');
      if (asideBtn) {
        asideBtn.addEventListener('click', (e) => {
          e.preventDefault();
          document.body.classList.toggle('show-aside');
        });
      }
    <?php } ?>
  })();
</script>
