<?php
/*
  David Bray
  BrayWorth Pty Ltd
  e. david@brayworth.com.au

  MIT License
*/  ?>

<script type="module">
  import { h, render } from 'preact';
  import { useState, useEffect } from 'hooks';
  import htm from 'htm';

  const html = htm.bind(h);
  const _ = window._brayworth_;

  const FooterComponent = () => {
    const [bootstrapVersion, setBootstrapVersion] = useState('');

    useEffect(() => setBootstrapVersion(_.bootstrap_version()), []);

    return html`
      <footer class="fixed-bottom">
        <div class="container-fluid">
          <div class="row mb-0">
            <div class="col text-muted">
              <em>BootStrap: <span>${bootstrapVersion}</span></em>
            </div>

            <div class="col-auto" id="brayworthLOGO">
              <a title="software by BrayWorth using php"
                href="https://brayworth.com" target="_blank">BrayWorth</a>
            </div>
          </div>
        </div>
      </footer>
    `;
  };

  document.addEventListener('DOMContentLoaded', () => {
    const root = document.createElement('div');
    document.body.appendChild(root);
    render(html`<${FooterComponent} />`, root);
  });
</script>
