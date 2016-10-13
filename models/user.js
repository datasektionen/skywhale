module.exports = function(sequelize, DataTypes) {
  var User = sequelize.define("User", {
    fullname: DataTypes.STRING,
    kthid: DataTypes.STRING,
  });
  return User;
};
