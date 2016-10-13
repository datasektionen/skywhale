var fetch = require('node-fetch');
var fs = require('fs');
var pug = require('pug');
var path = require('path');



var spam_api_key = process.env.SPAM_API_KEY;

exports.sendNewNomination = function(fullname, kthid, posts) {
  //Compose email.

  fs.readFile(path.join(__dirname,'../templates/newnomination.pug'), 'utf8', function (err, data) {
      if (err) throw err;
      console.log(data);
      var fn = pug.compile(data);
      var html = fn({fullname:fullname});
      res.send(html);
      console.log(html);
  });
  /*var html = 
  var email = {
      from: valberedning@d.kth.se, // sender address
      to: kthid + "@kth.se", // list of receivers
      subject: "Du har blivit nominerad!", // Subject line
      html: req.body.html // html body
  };*/
}