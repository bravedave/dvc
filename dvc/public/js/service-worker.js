/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
// Add dataCacheName here
var cacheName = 'dvc-active-cache';
var filesToCache = [
	'/',
	'/js/jquery-2.1.1.min.js',
	'/js/bootstrap.min.js',
	'/js/brayworth.js',
	'/css/bootstrap.min.css',
	'/css/dvc.css',
	'/css/font-awesome.min.css',
	'/css/custom.css',
	'/contact'
];

self.addEventListener('install', function(e) {
	console.log('[ServiceWorker] Install');
	e.waitUntil(
		caches.open(cacheName).then(function(cache) {
			console.log('[ServiceWorker] Caching App Shell');
			return cache.addAll(filesToCache);
		})
	);
});

self.addEventListener('activate', function(e) {
	console.log('[ServiceWorker] Activate');
	e.waitUntil(
		caches.keys().then(function(keyList) {
			return Promise.all(keyList.map(function(key) {
				console.log('[ServiceWorker] Removing old cache', key);
				if (key !== cacheName) {
					return caches.delete(key);
				}
			}));
		})
	);
});

self.addEventListener('fetch', function(e) {
	console.log('[ServiceWorker] Fetch', e.request.url);
	// Check if requested URL is for the data service here
	e.respondWith(
		caches.match(e.request).then(function(response) {
			if (response) {
				console.log( 'responding with cache');
				return response

			}
			else {
				fetch(e.request);

			}

		})
	);
});
