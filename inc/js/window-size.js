  // -----------
  // Debugger that shows view port size. Helps when making responsive designs.
  // -----------
  function showViewPortSize(display) {
    if(display) {
      var height = window.innerHeight;
      var width = window.innerWidth;
      jQuery('body').prepend('<div id="viewportsize" class="dev-" style="z-index:9999;absolute:fixed;top:2rem;right:1rem;color:#fff;background:#d29;padding:10px">Height: '+height+' || Width: '+width+'</div>');
      jQuery(window).resize(function() {
              height = window.innerHeight;
              width = window.innerWidth;
              jQuery('#viewportsize').html('Height: '+height+'<br>Width: '+width);
      });
    }
  }

  jQuery(document).ready(function(){
     showViewPortSize(true);
  });