var express = require('express');
var router  = express.Router();
var models = require('../models');
var mail = require('../util/mail');

router.post('/nominatefreetext',function(req, res) {
	//TODO: save to db somewhere
	//send mail to valberedning@d.kth.se. Someone wants to nominate Y.
});

router.get('/nominate', function(req, res) {
	//kthid
	mail.sendNewNomination("Jesper Simonsson", "jsimo", "studerandeskyddsombud");
});

module.exports = router;
