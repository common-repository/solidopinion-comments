



jQuery(document).ready(function() {

    
 jQuery(document).on('click', '.so_tabs', function(e){
      e.preventDefault();
      e.stopPropagation();
      //var current = jQuery(this);
      //var tab_val = current.attr('data-so-tab');
      /*if (current.is(':checked')) {
        //console.log(jQuery('select option[value="' + tab_val + '"]'));
        //jQuery('select option[value="' + tab_val + '"]').removeAttr('disabled');
      } else {
        //jQuery('select option[value="' + tab_val + '"]').attr('disabled', 'disabled');
      }*/
      return false;
            
      //current.closest('form').submit();
      //var is_tab_checked = current.is(':checked') ? 1 : 0;
      //alert(is_tab_checked);
    });

    

});