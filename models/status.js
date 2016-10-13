module.exports = function(sequelize, DataTypes) {
  var Status = sequelize.define("Status", {
    name: DataTypes.STRING //Name of status, ej svarat, tackat ja, tackat nej
  });
  return Status;
};
