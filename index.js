var express = require('express');
var bodyParser = require('body-parser');
var example = require('./routes/example');
var models = require("./models");

var app = express();
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

app.use('/', example);

var port = process.env.PORT || 5000;

models.sequelize.sync().then(function () {
  var server = app.listen(port, function() {
    console.log('Express server listening on port ' + server.address().port);
  });
});