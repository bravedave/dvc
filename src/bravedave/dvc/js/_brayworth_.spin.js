/**
 * Copyright (c) 2025 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 **/

(_ => {

  _.spin = () => `<div class="spinner-border ms-auto" aria-hidden="true"></div>`;

  _.spin.loading = p => {

    const options = {
      ...{
        margin: 'm-5',
        status: 'string' == typeof p ? p : 'Loading',
      }, ...p
    };

    return `<div class="d-flex align-items-center ${options.margin}"><strong role="status">${options.status}...</strong>
               <div class="spinner-border ms-auto" aria-hidden="true"></div></div>`
  }
})(_brayworth_);