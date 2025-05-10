<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * compatibility : bootstrap 5
*/

use dvc\theme;

$title = $title ?? $this->title;  ?>

<style>
  @media (max-width: 767px) {

    body:not(.show-aside) aside {
      display: none !important
    }

    body.show-aside main {
      display: none !important
    }
  }
</style>

<div id="<?= $_navbar = strings::rand() ?>"></div>

<script type="module">
  import {
    h,
    render
  } from 'preact';
  import {
    useState
  } from 'hooks';
  import htm from 'htm';

  const html = htm.bind(h);

  const NavbarComponent = ({
    title,
    links
  }) => {
    const [isAsideVisible, setAsideVisible] = useState(false);

    const toggleAside = () => {
      setAsideVisible(!isAsideVisible);
      document.body.classList.toggle('show-aside', !isAsideVisible);
    };

    return html`
      <nav class="<?= theme::navbar() ?>" role="navigation">
        <div class="container-fluid">
          <button type="button" class="navbar-toggler" onClick=${toggleAside}>
            <i class=${isAsideVisible ? 'bi bi-three-dots' : 'bi bi-three-dots-vertical'}></i>
          </button>

          <div class="navbar-brand">${title}</div>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbar-collapse" aria-controls="navbar-collapse"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="ms-auto navbar-nav">
              ${links.map(link => html`
                <li class="nav-item">
                  <a class="nav-link" href=${link.href}>
                    <i class=${link.icon}></i> ${link.text}
                  </a>
                </li>
              `)}
            </ul>
          </div>
        </div>
      </nav>
    `;
  };

  document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('<?= $_navbar ?>');

    const links = [{
        href: '/',
        icon: 'bi bi-house',
        text: 'Home'
      },
      {
        href: '/docs/',
        icon: 'bi bi-file-text',
        text: 'Docs'
      },
      {
        href: 'https://github.com/bravedave/',
        icon: 'bi bi-github',
        text: 'GitHub'
      },
    ];

    render(html`<${NavbarComponent} title="${document.title}" links=${links} />`, root);
  });
</script>