jQuery(document).ready(function ($){
           $.ajax({
             url: php_arrayload.admin_ajax,
             type:'POST',
             data: ({
               'action': 'load-scripts',
             }),             
             beforeSend: function () {
                          
            },
             success: function (data, textStatus, jqXHR) {
                
            },
             error: function (jqXHR, textStatus, errorThrown) {
              console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
             }
           });
        
});
