MsgElem = document.getElementById("msg")
TokenElem = document.getElementById("token")
NotisElem = document.getElementById("notis")
ErrElem = document.getElementById("err")
// Initialize Firebase
// TODO: Replace with your project's customized code snippet
var config = {
    // apiKey: "AIzaSyBeUSyithvApTQU-pFHWwkEPk1woOf1vZs",
    // authDomain: "nuup-613fd.firebaseapp.com",
    // databaseURL: "https://nuup-613fd.firebaseio.com",
    // projectId: "nuup-613fd",
    // storageBucket: "nuup-613fd.appspot.com",
    // messagingSenderId: "596787191407"
    apiKey: "AIzaSyBeUSyithvApTQU-pFHWwkEPk1woOf1vZs",
    authDomain: "nuup-613fd.firebaseapp.com",
    databaseURL: "https://nuup-613fd.firebaseio.com",
    projectId: "nuup-613fd",
    storageBucket: "nuup-613fd.appspot.com",
    messagingSenderId: "596787191407",
    appId: "1:596787191407:web:d5726bb5570ade236c379a",
    measurementId: "G-NB0PJEXXFK"
};
// firebase.initializeApp(config);
// if (!firebase.apps.length) {
firebase.initializeApp(config);
//  }else {
//     firebase.app(); // if already initialized, use that one
//  }
const messaging = firebase.messaging();

console.log("==> " + messaging.getToken() + "<===");


messaging
    .requestPermission()
    .then(function () {
        MsgElem.innerHTML = "Notification permission granted."
        console.log("Notification permission granted.");

        // get the token in the form of promise
        return messaging.getToken()
    })
    .then(function (token) {
        TokenElem.innerHTML = "token is : " + token
        console.log("EL TOKEN SE MUEVE" + token);
        subscribeTokenToTopic(token);
    })
    .catch(function (err) {
        ErrElem.innerHTML = ErrElem.innerHTML + "; " + err
        console.log("Unable to get permission to notify.", err);
    });
messaging.onMessage(function (payload) {
    // console.log("Message received. ", payload);
    // NotisElem.innerHTML = NotisElem.innerHTML + JSON.stringify(payload) 
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon,
    };

    var notification = new Notification(notificationTitle, notificationOptions);
    notification.onclick = function (event) {
        event.preventDefault(); // prevent the browser from focusing the Notification's tab
        window.open(payload.notification.click_action, '_blank');
        notification.close();
    }

    if (!("Notification" in window)) {
        console.log("This browser does not support system notifications");
    }
    // Let's check whether notification permissions have already been granted
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        var notification = new Notification(notificationTitle, notificationOptions);
        notification.onclick = function (event) {
            event.preventDefault(); // prevent the browser from focusing the Notification's tab
            window.open(payload.notification.click_action, '_blank');
            notification.close();
        }
    }
});





function subscribeTokenToTopic(token) {
    $.ajax({
        url: "../ws/subFireWebToken",
        type: "POST",
        data: { token: token },
        cache: false,
        success: function (html) {
            console.log("token " + token + " suscrito");
        },
        error: function (error) {
            console.log(error)
        }
    });
}



