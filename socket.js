var server = require('http').Server();

var io = require('socket.io')(server);

// // Sample of socket.io communication w/o Redis
// io.on('connection', function(socket) {
// 	socket.emit('news', { hello: 'world'});
// 	socket.on('my other event', function(data) {
// 		console.log(data);
// 	});
// });

var Redis = require('ioredis');
// var redis = new Redis(6379, '52.15.200.179'); // Fit A.I AWS Instance
var redis = new Redis();
console.log(redis);

redis.subscribe('lifts');
// redis.on('message', function(channel, message) {

//         message = JSON.parse(message);
//         console.log(message);

//         io.emit(channel + ':' + message.event, message.data);
// });

redis.on('message', function(channel, message) {
    console.log('channel: ' + channel);
    console.log('message: ' + message);

    io.emit(channel, message);
});

server.listen(3000, function() {
	console.log('Socket.io connected');
});