var express = require('express');
var bodyParser = require('body-parser');
var models = require("./models");

var example = require('./routes/example');
var nominating = require('./routes/nominating');

var app = express();
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

app.use('/', example);
app.use('/nominate', nominating);

var port = process.env.PORT || 5000;

models.sequelize.sync().then(function () {
  var server = app.listen(port, function() {
    console.log('Express server listening on port ' + server.address().port);
  });
});