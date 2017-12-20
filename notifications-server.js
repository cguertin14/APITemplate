// Configure settings to allow
// notifications to be sent afterwards.
// Also, apple key is necessary from
// developer account.
var options = {
    token: {
        key: "path/to/APNsAuthKey_XXXXXXXXXX.p8",
        keyId: "key-id",
        teamId: "developer-team-id"
    },
    production: false
};

var apn = require('apn');
var apnProvider = new apn.Provider(options);

// Network stuff
var connections = [];
const express = require('express'),
      app     = express(),
      path    = require('path'),
      http    = require('http').Server(app),
      io      = require('socket.io')(http),
      router  = express.Router();

http.listen(3000, function () {
    console.log('Listening on Port 3000');
});

io.on('connection', function (socket) {
    connections.push(socket);
    console.log('Connected: %s sockets connected', connections.length);

    // Disconnect
    socket.on('disconnect', function (data) {
        connections.splice(connections.indexOf(socket), 1);
        console.log('Disconnected: %s sockets connected', connections.length);
    });

    // Send new notification to client
    socket.on('send notification', function (data) {
        io.sockets.emit('new notification', sendNewNotification(data));
    });
});


// Send notification function
function sendNewNotification(data) {
    let deviceToken = data.deviceToken;
    let notifPayload = [];
    let newNotification = new apn.Notification();
    let toReturn = null;

    notifPayload[data.notification.type] = data.notification.body;
    newNotification.alert = data.notification.message;
    newNotification.sound = 'ping.aiff';
    newNotification.payload = notifPayload;
    newNotification.body = data.notification.body;

    apnProvider.send(newNotification, deviceToken).then( (result) => {
        // see documentation for an explanation of result
        toReturn = result;
    });
    return toReturn;
}