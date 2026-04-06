/**
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * Robust CSV export using Papa Parse for RFC 4180 compliance
 * test: _brayworth_.csv.call( <a table>, 'filename.csv');
*/

(_ => {

	_.csv = function (fileName) {

		if (!this.tagName) throw 'Error: Not a valid table element';
		if (!/table/i.test(this.tagName)) throw 'Error: Invalid table element (tagName mismatch)';

		const table = $(this);
		let data = [];

		// Extract header row (handles both <th> and <td>)
		let headerRow = table.find('thead > tr').last();
		if (headerRow.length > 0) {
			let headerCells = [];
			headerRow.find('> th, > td').each((i, el) => {
				let text = String($(el).text()).trim();
				if (text === '') text = $(el).attr('title') || '';
				headerCells.push(text);
			});
			if (headerCells.length > 0) {
				data.push(headerCells);
			}
		}

		// Extract data rows (skip hidden rows)
		table.find('tbody > tr:not(.d-none)').each((i, tr) => {
			let rowCells = [];
			$('> td', tr).each((i, el) => {
				let value = $(el).text();

				// Special handling for first column: use image title if available
				if (i === 0) {
					let imgElement = $('img', el);
					if (imgElement.length > 0) {
						value = imgElement.first().attr('title') || value;
					}
				}

				// Trim but preserve falsy values (0, false, null, empty string)
				value = String(value || '').trim();

				rowCells.push(value);
			});
			if (rowCells.length > 0) {
				data.push(rowCells);
			}
		});

		// Use Papa Parse if available, otherwise fallback to manual CSV generation
		if ('undefined' !== typeof window.Papa) {
			csvDataWithPapa(data, fileName);
		} else {
			csvDataFallback(data, fileName);
		}
	};

	// Primary method: Use Papa Parse (RFC 4180 compliant, robust)
	const csvDataWithPapa = (data, fileName) => {

		if (!data || data.length === 0) {
			console.warn('CSV export: no data to export');
			return;
		}

		try {
			// Papa.unparse handles all CSV escaping correctly
			let csvContent = window.Papa.unparse(data, {
				header: false,
				dynamicTyping: false,
				skipEmptyLines: false
			});

			downloadBlob(csvContent, fileName);
		} catch (e) {
			console.error('Papa Parse error:', e);
			// Fallback to manual method
			csvDataFallback(data, fileName);
		}
	};

	// Fallback method: Manual CSV generation if Papa Parse not loaded
	const csvDataFallback = (data, fileName) => {

		if (!data || data.length === 0) {
			console.warn('CSV export: no data to export');
			return;
		}

		let csvContent = '';
		data.forEach((row, index) => {
			let rowString = row.map(cell => {
				// Ensure string value
				let value = String(cell || '').trim();

				// CSV escape: quote if contains special chars, escape internal quotes
				if (/"/.test(value) || /[,\n\r]/.test(value)) {
					value = '"' + value.replace(/"/g, '""') + '"';
				}

				return value;
			}).join(',');

			csvContent += rowString + (index < data.length - 1 ? '\n' : '');
		});

		downloadBlob(csvContent, fileName);
	};

	// Helper: Create blob and trigger download
	const downloadBlob = (csvContent, fileName) => {

		let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
		let blobUrl = URL.createObjectURL(blob);
		let link = document.createElement('a');

		link.setAttribute('href', blobUrl);
		link.setAttribute('download', fileName || 'export.csv');
		document.body.appendChild(link);
		link.click();

		// Cleanup
		document.body.removeChild(link);
		URL.revokeObjectURL(blobUrl);
	};

	// Async loader for Papa Parse (lazy-loads if not present)
	_.csv.papaparse = () => {
		const papaURL = _.url("assets/papaparse/");

		return 'undefined' === typeof window.Papa ?
			new Promise(resolve => {
				_.get.script(papaURL)
					.then(() => {
						resolve(window.Papa);
					});
			}) :
			Promise.resolve(window.Papa);
	};

	// jQuery plugin for convenience
	$.fn.downloadCSV = function (filename) {
		if (this.length === 0) {
			console.error('downloadCSV: no table element found');
			return this;
		}
		_.csv.call(this[0], filename);
		return this;  // Allow chaining
	};
})(_brayworth_);
