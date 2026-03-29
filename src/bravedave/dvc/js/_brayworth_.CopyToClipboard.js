/**
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * Clipboard helper
 *
 * _.CopyToClipboard(value) accepts either:
 * - a DOM Element (copies element text/value)
 * - a string/number/boolean (copies text directly)
 *
 * Behaviour:
 * - tries navigator.clipboard.writeText first
 * - falls back to document.execCommand('copy') using a hidden textarea
 * - resolves with the original input value on success
 * - rejects with an Error on failure
 *
 * */

(_ => _.CopyToClipboard = value => new Promise((resolve, reject) => {
  const fail = message => {
    _.growlError(message);
    reject(new Error(message));
  };

  const ok = () => {
    _.growlSuccess('Content Copied to Clipboard');
    resolve(value);
  };

  const toText = input => {
    if ('string' == typeof input) return input;
    if ('number' == typeof input || 'boolean' == typeof input) return String(input);

    if (input instanceof Element) {
      if ('value' in input && 'string' == typeof input.value) return input.value;
      return input.innerText || input.textContent || '';
    }

    return '';
  };

  const copyWithExecCommand = text => {
    if (!document.execCommand || !document.body) return false;

    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', 'readonly');
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    textarea.style.pointerEvents = 'none';
    textarea.style.left = '-9999px';

    document.body.appendChild(textarea);
    textarea.select();
    textarea.setSelectionRange(0, textarea.value.length);

    const copied = document.execCommand('copy');
    document.body.removeChild(textarea);

    return !!copied;
  };

  const text = toText(value);
  if (!text) return fail('Error: Nothing to copy');

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text)
      .then(ok)
      .catch(() => {
        if (copyWithExecCommand(text)) {
          ok();
        }
        else {
          fail('Error: Copy to Clipboard');
        }
      });
  }
  else if (copyWithExecCommand(text)) {
    ok();
  }
  else {
    fail('Error: Copy to Clipboard');
  }
}))(_brayworth_);
