module.exports = function(sequelize, DataTypes) {
  var Nomination = sequelize.define("Nomination", {
    info: DataTypes.STRING, //Future use
  }, {
    classMethods: {
      associate: function(models) {
        Nomination.hasOne(models.Status);
        Nomination.hasOne(models.User);
        Nomination.belongsTo(models.ValPost);
      }
    }
  });
  return Nomination;
};
