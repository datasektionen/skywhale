var express = require('express');
var router  = express.Router();

router.use('/', function(req, res) {
  res.status(418);
  res.send("hello world from the teapot");
});

module.exports = router;
