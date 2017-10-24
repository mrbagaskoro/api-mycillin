$(document).ready(function(){
    var srv = "http://mycillin.com/services/";
    
    $("#btn-submit-regis").click(function(){
        var name=$("#inp-fname").val()+" "+$("#inp-lname").val();
        var email=$("#inp-email").val();
        var password=$("#inp-password").val();
        var ref_id=$("#inp-rfid").val();

        console.log(name, email, password, ref_id);
        send_reg_data(name, email, password, ref_id);
    });
});

function send_reg_data(name, email, password, ref_id){
    $("#btn-submit-regis").text('Sending...').prop('disabled', true);
    $.ajax({
        url: "http://mycillin.com/services/api/register",
        type: "POST",
        data: {name:name,email:email,password:password,ref_id:ref_id},
        success: function(response){
            console.log(response);
            $("#btn-submit-regis").text('Submitted').prop('disabled', true);
            //alert('Registration success. Please check your email to confirm account activation');
            Bootpop.alert('Registration success. Please check your email to confirm account activation.', {
                title: "Success!",
                size: "small"
            });
        },
        error: function (e) {
            console.log(e.responseText, e.status, e.statusText, e.responseJSON);
            $("#btn-submit-regis").text('Submit').prop('disabled', false);
            //alert('Registration failed : '+e.responseJSON.result.message);
            Bootpop.alert('Registration failed : '+e.responseJSON.result.message, {
                title: "Failed!",
                size: "small"
            });
        }
    });
}