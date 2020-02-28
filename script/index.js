$(function() {
    $("input").attr("autocomplete", "off");
  
    $("#submitForm").on("click", function(e) {
      // validate password here
      var len = pas.length;      
      if(len < 8) {
          // Prevent form submission
          $("#error").html("<b class='text-danger'>Password cannot be less than 8 characters</b>");
          e.preventDefault();
      }
       
      if($('#password').val() !== $('#cpassword').val()) {
          // Prevent form submission
          $("#error").html("<b class='text-danger'>Passwords do not match</b>");
          e.preventDefault();
      }
      // validate email and other form fields here
      var regEx = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      if($("#email").val() != regEx.pattern()){
        
        $("#error").html("<b class='text-danger'>Invalid email</b>");
        e.preventDefault();
      }
      if ($("#tac").prop("checked")) {
        $("#myForm").submit();
      }
    });
  });
  