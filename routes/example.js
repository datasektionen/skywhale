var express = require('express');
var router  = express.Router();
var models = require('../models');

router.use('/teapot', function(req, res) {
  res.status(418);
  res.send("hello world from the teapot");
});

router.get('/sm', function(req, res) {
  models.SM.findAll({}).then(function(sm) {
    res.send(sm);
  });
});

module.exports = router;
