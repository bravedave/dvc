import { h, render } from 'preact';
import htm from 'htm';

const html = htm.bind(h);

function renderToNode(vnode) {
  const mount = document.createElement('div');
  render(vnode, mount);
  return mount.firstElementChild;
}

export { renderToNode };