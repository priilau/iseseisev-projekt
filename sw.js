var CACHE_NAME = 'my-site-cache-v2';
var urlsToCache = [
  'https://caupo.ee/messenger/',
  'https://caupo.ee/messenger/index.php',
  'https://caupo.ee/messenger/main.php',
  'https://caupo.ee/messenger/newuser.php',
  'https://caupo.ee/messenger/functions.php',
  'https://caupo.ee/messenger/config.php',
  'https://caupo.ee/messenger/192.png',
  'https://caupo.ee/messenger/512.png',
  'https://caupo.ee/messenger/favicon.png',
  'https://caupo.ee/messenger/main.js',
  'https://caupo.ee/messenger/style.css',
  'https://caupo.ee/messenger/manifest.json'
];

self.addEventListener('install', function(event) {
  // Perform install steps
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        //console.log('Opened cache');
		    //console.log(urlsToCache);
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        // Cache hit - return response
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});
