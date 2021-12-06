document.addEventListener("DOMContentLoaded", function(){

    console.log('script working');
    
    User=document.querySelector("#user_login");
    Password=document.querySelector("#user_pass");
    
    var Login=document.querySelector("#wp-submit");
    if(Login){
        Login.addEventListener("click",Validate_input_login);
    }


    var register=document.querySelector('#register');
    if(register){
        register.addEventListener("click", gotoURL);
    }
});


function gotoURL(e){

        //console.log('assigned');
        e.preventDefault();
        var baseurl = window.location.origin;
        window.location.assign(baseurl+'/register');
    
    
}



function Validate_input_login(e){
    console.log('validating login input');
    var Errors=[];
    var i=0;
    clearErrors();

   
    if(User.value.length == 0){

        Errors[i]='Username is empty';
        e.preventDefault();
        i++;
    }
    if(Password.value.length == 0){

        Errors[i]='Password is empty';
        e.preventDefault();
        i++;
    }

    
    
    if (Errors.length > 0){

        console.log('appending errors'); 
        var LoginForm=document.querySelector("#loginform-custom");   
        var LoginMsg=document.createElement("p");
        LoginMsg.setAttribute("class", "login-msg");
        
        document.querySelector('main').insertBefore(LoginMsg,LoginForm);
        for(i=0;i<Errors.length;i++){

            LoginMsg.innerHTML+=`<span>${Errors[i]}</span><br>`;
        }

        e.preventDefault();
    }



}


function clearErrors(){
    var LoginMsg=document.querySelector(".login-msg");
    if (LoginMsg){
        LoginMsg.innerHTML='';
        LoginMsg.remove();

        
    }

}