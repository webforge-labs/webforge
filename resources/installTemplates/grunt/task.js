
/*
* webforge-js-shimney
* http://www.ps-webforge.com/
*
* Copyright (c) 2013 Philipp Scheit
* Licensed under the MIT license.
*/

module.exports = function(grunt) {
  'use strict';

  grunt.registerMultiTask('shimney', 'Manage cherishd shim repositories.', function () {
    var shim = {
      name: this.target
    };

    grunt.verbose.writeflags(shim);


  });
};