define(['jquery', 'app/boot', 'joose'], function ($, boot) {
  var $cmsContent = $('#content'), main;
  
  // there might be cases (e.g. login page) where #content is not avaible
  if ($cmsContent.length) {
    main = boot.createMain($cmsContent);

    // load tiptoi.main aus dem body
    main.getLoader().finished();
  }
  
  return main;
});