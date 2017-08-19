$( function() {
    
    var token = $('#token').val();
    $("#supplier_box").autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "/api/supplier/search/" + request.term + "/?token=" + token,
          // dataType: "jsonp",
          success: function( data ) {
            response($.map((data.data), function (item) {                                
                var AC = new Object();

                //autocomplete default values REQUIRED
                AC.label = item.name;
                AC.value = item.id;

                return AC
            }));   
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) { 
            alert("Something has gone wrong. Please refresh."); 
          }
        });
      },
      minLength: 3,
      select: function (event, ui) {                    
        $("#supplier_box").val(ui.item.name);
     }     
    });

  $("button#btn_delete").on('click',function(){
    alert('removing');
    $(this).parent().parent().remove();
  });

  $("div", "div#extra-fields").hide();

  $("select#role").change(function(){
        // hide previously shown in target div
        $("div", "div#extra-fields").hide();
        
        // read id from your select
        var value = $(this).val();
        // show rlrment with selected id
        $("div#"+value).show();
    });
});