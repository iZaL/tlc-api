var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();

redis.psubscribe('*', function(err, count) {
  // console.log('err',err);
  console.log('count',count);
});

redis.on('pmessage', function ( subscribed, channel, payload) {
  payload = JSON.parse(payload);
  payload.channel = channel;
  // console.log('subscribed', subscribed);
  console.log('channel', channel);
  console.log('payload', payload);
  io.sockets.in(channel).emit(payload.event, payload.data);
});

var users = [];

io.on('connection', function (socket) {
  console.log('connected');

  // connect a user to the socket network
  socket.on('user.connected', function (userID) {
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

redis.on("error", function (err) {
  console.log('redis error',err);
});