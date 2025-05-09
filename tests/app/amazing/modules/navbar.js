import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import htm from 'htm';

const html = htm.bind(h);

const Navbar = ({ title = 'Default Title', theme = 'navbar navbar-dark text-bg-primary navbar-expand-md' }) => {
  const [showAside, setShowAside] = useState(false);
  const [navCollapsed, setNavCollapsed] = useState(true);

  const toggleAside = () => {
    setShowAside((prev) => {
      const newState = !prev;
      document.body.classList.toggle('show-aside', newState); // Toggle 'show-aside' class
      return newState;
    });
  };

  const toggleNav = () => setNavCollapsed(!navCollapsed);

  useEffect(() => {
    // Cleanup to ensure 'show-aside' is removed when the component unmounts
    return () => document.body.classList.remove('show-aside');
  }, []);

  return (
    html`<nav className="${theme}" role="navigation">
      <div className="container-fluid">
        <button type="button" className="navbar-toggler" onClick=${() => toggleAside()}>
          <i className="bi ${showAside ? 'bi-three-dots' : 'bi-three-dots-vertical'}"></i>
        </button>

        <div className="navbar-brand">${title}</div>

        <button
          className="navbar-toggler"
          type="button"
          onClick=${() => toggleNav()}
          aria-expanded={!navCollapsed}
          aria-label="Toggle navigation"
        >
          <span className="navbar-toggler-icon"></span>
        </button>

        <div className="collapse navbar-collapse ${navCollapsed ? '' : 'show'}">
          <ul className="ms-auto navbar-nav">
            <li className="nav-item">
              <a className="nav-link" href="/">
                <i className="bi bi-house"></i> Home
              </a>
            </li>
            <li className="nav-item">
              <a className="nav-link" href="/docs/">
                <i className="bi bi-file-text"></i> Docs
              </a>
            </li>
            <li className="nav-item">
              <a className="nav-link" href="https://github.com/bravedave/">
                <i className="bi bi-github"></i> GitHub
              </a>
            </li>
          </ul>
        </div>
      </div>

      <style>
          @media (max-width: 767px) {
            body:not(.show-aside) aside {
              display: none !important;
            }
            body.show-aside main {
              display: none !important;
            }
          }
      </style>
    </nav>`
  );
};

export default Navbar;
