module.exports = function(sequelize, DataTypes) {
  var Example = sequelize.define("Example", {
    ex: DataTypes.STRING,
  });
  return Example;
};
