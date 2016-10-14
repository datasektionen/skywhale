var express = require('express');
var fs = require('fs');
var pug = require('pug');
var path = require('path');
var app = express();

app.get('/', function(req, res) {
	fs.readFile(path.join(__dirname,'./templates/newnomination.pug'), 'utf8', function (err, data) {
	    if (err) throw err;
	    var fn = pug.compile(data);
	    var html = fn({fullname:"Jesper Simonsson", date:"17:e maj", sm:"Val-SM", posts:['studerandeskyddsombud']});
	    res.send(html);
	});
});

var port = 5000;
var server = app.listen(port, function() {
  console.log('Express server listening on port ' + server.address().port);
});