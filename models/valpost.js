module.exports = function(sequelize, DataTypes) {
  var ValPost = sequelize.define("ValPost", {
    postIdentifier: DataTypes.STRING, //Dfunkt identifier
    postName: DataTypes.STRING, //Dfunkt name
    start: DataTypes.DATEONLY,
    end: DataTypes.DATEONLY,
  }, {
    classMethods: {
      associate: function(models) {
        ValPost.belongsTo(models.SM);
        ValPost.hasMany(models.Nomination);
      }
    }
  });
  return ValPost;
};
