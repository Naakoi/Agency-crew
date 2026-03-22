importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

firebase.initializeApp({
  apiKey: "AIzaSyAM2hqloZX6Qu1sshQIhQcpzAllr--hPgc",
  authDomain: "agency-accommodation.firebaseapp.com",
  projectId: "agency-accommodation",
  storageBucket: "agency-accommodation.firebasestorage.app",
  messagingSenderId: "1034441888269",
  appId: "1:1034441888269:web:f59f94a91679c87cb33b44"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/icon-192x192.png',
    badge: '/icon-192x192.png'
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
