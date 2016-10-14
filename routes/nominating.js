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
	//1. Check if the given kthid has already been nominated for that post.
	// 	 (if already nominated return error)
	//2. Get user data from zfinger
	//3. Add nomination to the database
	//4. Send email to nominated person
	//   (with the correct name for sm, date and such)
	//5. Return success!
	mail.sendNewNomination("Lili Du", "lilid", ["ordförande"], "17:e maj", "Val-SM").then(function(bla) {
		res.send(bla);
	}).catch(function(err) {
		res.send(err);
	});
});

module.exports = router;
