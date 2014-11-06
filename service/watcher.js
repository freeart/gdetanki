var pg = require('pg');
var express = require('express');
var app = express();

var d = require('domain').create();
d.on('error', function (er) {
	console.error('watcher domain', er.message, er.stack);
	process.exit(0);
});
d.run(function () {
	app.use(function (req, res, next) {
		res.header("Access-Control-Allow-Origin", "*");
		res.header("Access-Control-Allow-Headers", "X-Requested-With");
		next();
	});

	app.use(function (req, res, next) {
		if (req.headers.origin) {
			res.header('Access-Control-Allow-Origin', '*')
			res.header('Access-Control-Allow-Headers', 'X-Requested-With,Content-Type,Authorization')
			res.header('Access-Control-Allow-Methods', 'GET,PUT,PATCH,POST,DELETE')
			if (req.method === 'OPTIONS') return res.send(200)
		}
		next();
	});

	var server = require('http').createServer(app);
	server.listen(3000, function () {
		console.log('server listening on port 3000');
	});
	var io = require('socket.io').listen(server, {log: true});

	var pgListener = new pg.Client("postgres://gdetanki:root@localhost/gdetanki");
	pgListener.connect(function (err) {
		console.info('ready to listen db...');
		var query = pgListener.query("LISTEN watch");
		query.on('error', function (err) {
			console.log("LISTEN watch", {name: "watch", err: err});
		});
	});

	pgListener.on('notification', function (msg) {
		var data = msg.payload.split('::');
		console.log('watch', {id: data[2], action: data[0].toLowerCase(), entity: data[1].toLowerCase()});
		io.emit('watch', {id: data[2], action: data[0].toLowerCase(), entity: data[1].toLowerCase()});
	});
});