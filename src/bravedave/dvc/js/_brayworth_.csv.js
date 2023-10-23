/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * test: _brayworth_.csv.call( <a table>, 'filename.csv');
*/

(_ => {

	_brayworth_.csv = function (fileName) {

		if (!this.tagName) throw 'Not a table';
		if (!/table/i.test(this.tagName)) throw 'Not a table (tagName)';

		const table = $(this);

		let data = [];

		let r = table.find('thead > tr').last();
		if (r.length > 0) {

			let a = [];
			r.find('> td').each((i, el) => a.push($(el).text()));
			data.push(a);
		}

		table.find('tbody > tr:not(.d-none)').each((i, tr) => {

			let a = [];
			$('> td', tr).each((i, el) => {

				let s = $(el).text();

				if (i == 0) {

					let els = $('img', el);
					if (els.length > 0) s = els.first().attr('title');
				}

				if (undefined == s) s = '';
				s = String(s).replace(/"/g, '""'); // escape double quotes;
				if (/(,|\n|')/g.test(s)) s = '"' + s.trim() + '"';	// add quotes if contains a comma
				a.push(s.trim());
			});
			data.push(a);
		});

		return csvData(data, fileName);
	};

	const csvData = (data, fileName) => {

		//~ console.log( data);

		let csvContent = '';	// data:text/csv;charset=utf-8;
		data.forEach((a, i) => {
			// let dataString = JSON.stringify(a);
			let dataString = String(a);
			dataString = dataString.replace(/(^\[|\]$)/g, '');	// remove enclosing []
			csvContent += i < data.length ? dataString + "\n" : dataString;
		});

		let blob = new Blob([csvContent], { type: "text/csv;charset=utf-8" });
		((blob) => {

			let href = URL.createObjectURL(blob);
			let a = document.createElement('a');
			a.setAttribute('href', href);
			a.setAttribute('download', !!fileName ? fileName : 'data.csv');
			document.body.appendChild(a); // Required for FF

			a.click(); // This will download the data file named "my_data.csv".

			URL.revokeObjectURL(a.href);
			a.remove();
		})(blob);

		return blob.text();
	}
})(_brayworth_);
