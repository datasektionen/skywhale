var fs = require('fs');
var pug = require('pug');
var path = require('path');
var http = require('http');
var https = require('https');
var querystring = require('querystring');


var spam_api_key = process.env.SPAM_API_KEY;

var sendEmail = function(email, resolve, reject) {
  email.key = spam_api_key; //Lets not forget this...

  var data = querystring.stringify(email);

  var options = {
      host: 'spam.datasektionen.se',
      port: 443,
      path: '/api/sendmail',
      method: 'POST',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Content-Length': Buffer.byteLength(data)
      }
  };

  var req = https.request(options, function(res) {
      res.setEncoding('utf8');
      res.on('data', function (chunk) {
        console.log("body: " + chunk);
        resolve(chunk);
      });
  });

  req.write(data);
  req.end();
}

exports.sendNewNomination = function(fullname, kthid, posts, date, sm) {
  return new Promise(function(resolve, reject) {
    fs.readFile(path.join(__dirname,'../templates/newnomination.pug'), 'utf8', function (err, data) {
        if (err) throw err;
        var fn = pug.compile(data);
        var html = fn({fullname:fullname, date:date, sm:sm, posts:posts});

        var email = {
          from: "valberedning@d.kth.se",
          to: kthid + "@kth.se",
          subject: "Du har blivit nominerad",
          html: html
        }
        sendEmail(email, resolve, reject);
    });
  });
}