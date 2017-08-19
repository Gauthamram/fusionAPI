$(document).ready(function(){
    //Fancybox for password recovery
    $("#recovery").fancybox({ 
        maxWidth: 600,
        maxHeight: 600,
        fitToView: false,
        width: '70%',
        height: '90%',
        autoSize: false
    });

    $('#recovery-form').bind("submit",function(e) {
        e.preventDefault();
        $.ajax({
            method: "POST",
            url: "/portal/user/recovery?token=" + token,
            data        : $(this).serializeArray(),
        success: function( data ) {
            if(data.status == 'success'){
              var html = "<div class='alert alert-success'><ul>";  
            } else {
              var html = "<div class='alert alert-danger'><ul>";
            }
            
            var list = "";
            $.each(data.result, function(key,value){
              list = list + "<li>" + value + "</li>";
            });

            html = html + list + "</ul></div>";
            $(".fancybox-slide div #page-inner .row .col-md-6").prepend(html);
          },
          error: function() { 
            alert("Something has gone wrong. Please refresh."); 
          }
        });
    });

});