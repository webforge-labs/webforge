/*globals requirejs*/
requirejs.config({
  baseUrl: "/psc-cms-js/lib",

  paths: {
    app: '/js'
  },

  map: {
    '*': {
      "app/boot": "boot"
    }
  }
});

define(['boot-helper', 'require'], function (boot, require) {
  
  require(['app/main']);

  return boot;
});