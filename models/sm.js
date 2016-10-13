module.exports = function(sequelize, DataTypes) {
  var SM = sequelize.define("SM", {
    name: {type: DataTypes.STRING, unique: true}, //There is only of each sm.
  }, {
    classMethods: {
      associate: function(models) {
        SM.hasMany(models.ValPost);
      }
    }
  });
  return SM;
};
