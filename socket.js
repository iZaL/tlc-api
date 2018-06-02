var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();

/** REDIS */
redis.psubscribe('*', function (err, count) {
  console.log('subscribing to redis');
  console.log('socket connected users',count);
  if (err) {
    console.log('Redis could not subscribe.', err);
  }
});

redis.on('pmessage', function ( subscribed, channel, payload) {
  console.log('new message on channel', channel);
  payload = JSON.parse(payload);
  payload.channel = channel;
  io.sockets.in(channel).emit(payload.event, payload.data);
});

redis.on("error", function (err) {
  console.log('redis error', err);
});

var users = [];

io.on('connection', function (socket) {
  console.log('connected');

  // connect a user to the socket network
  socket.on('user.connected', function (userID) {
    console.log('user.connected',userID);
    users.indexOf(userID) === -1 && users.push(userID);
    socket.user = userID;
  });

  socket.on('trip.track.subscribe', function (tripID) {
    console.log('connected to trip ',tripID);
    if (tripID) {
      socket.join('trip.track.' + tripID);
    }
  });

  socket.on('disconnect', function () {
    console.log('disconnected');

    var index = users.indexOf(socket.user);
    if (index > -1) {
      users.splice(index, 1);
    }
  });
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});
