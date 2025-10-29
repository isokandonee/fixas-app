$(function() {
    $("input").attr("autocomplete", "off");
  
    $("#submitForm").on("click", function(e) {     
      if ($("#tac").prop("checked")) {
        $("#myForm").submit();
      }
    });
  });
  