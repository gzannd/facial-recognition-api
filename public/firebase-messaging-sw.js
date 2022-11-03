importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyA50PwR_B-aWM--NuwWLBdbUng6hFn2Jc4",
    projectId: "securitysystem-f3a1b",
    messagingSenderId: "534625255689",
    appId: "1:534625255689:web:32e1831c3f390e778e53d8"
});


const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    return self.registration.showNotification(title,{body,icon});
});
