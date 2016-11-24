var gulp       = require('gulp');
var elixir     = require('laravel-elixir');
var browserify = require('browserify');
var source     = require('vinyl-source-stream');
var reactify   = require('reactify');

elixir((mix) => {
    mix.webpack('component.jsx');
});
