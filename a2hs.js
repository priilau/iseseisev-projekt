/*jshint esversion: 6*/

//let deferredPrompt;
let homeScreenbtn = document.querySelector(".a2hs");

	if ('serviceWorker' in navigator) {
	  window.addEventListener('load', function() {
		navigator.serviceWorker.register('https://caupo.ee/messenger/sw.js').then(function(registration) {
		  // Registration was successful
		  console.log('ServiceWorker registration successful with scope: ', registration.scope);
		}, function(err) {
		  // registration failed :(
		  console.log('ServiceWorker registration failed: ', err);
		});
	  });
	}

	window.addEventListener('beforeinstallprompt', (e) => {
	  // Prevent Chrome 67 and earlier from automatically showing the prompt
	  e.preventDefault();
	  // Stash the event so it can be triggered later.
	  deferredPrompt = e;
	  // Update UI notify the user they can add to home screen
	  homeScreenbtn.style.display = 'block';
	});
	homeScreenbtn.addEventListener('click', (e) => {
	  // hide our user interface that shows our A2HS button
	  homeScreenbtn.style.display = 'none';
	  // Show the prompt
	  deferredPrompt.prompt();
	  // Wait for the user to respond to the prompt
	  deferredPrompt.userChoice
		.then((choiceResult) => {
		  if (choiceResult.outcome === 'accepted') {
			console.log('User accepted the A2HS prompt');
		  } else {
			console.log('User dismissed the A2HS prompt');
		  }
		  deferredPrompt = null;
		});
	});