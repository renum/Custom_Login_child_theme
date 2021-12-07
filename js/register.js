document.addEventListener("DOMContentLoaded", 
                            function (){

                                console.log('dom loaded');
                                
                                ErrorList=document.querySelector('.form-errors');
                                SuccessMsg=document.querySelector('.form-success');
                                Name= document.querySelector("input[name='username']");
                                Email= document.querySelector("input[name='email']");
                                                                
                                                                
                                Password=document.querySelector("input[name='password']");

                                Password2=document.querySelector("input[name='password_confirmation']");
                                
                                
                                Consent=document.querySelector('#terms');
                                Register=document.querySelector("#submitbtn");
                                if(Register){
                                    Register.addEventListener("click", Validate_input);
                                }
                                
                            }


);


function Validate_input(e){

    var Errors=[];
    var i=0;
    clearErrors();

   
    if(Name.value.length == 0){

        Errors[i]='Username is empty';
        e.preventDefault();
        i++;
    }
    if(Email.value.length == 0){

        Errors[i]='Email is empty';
        e.preventDefault();
        i++;
    }

    if(Password.value.length == 0){

        Errors[i]='Password is empty';
        e.preventDefault();
        i++;
    }

    if(Password2.value.length == 0){

        Errors[i]='Password confirmation is empty';
        e.preventDefault();
        i++;
    }
    console.log(Consent);

    if(!(Consent.checked)){
        Errors[i]='Please check the consent box';
        e.preventDefault();
    }
    console.log(Errors);
    
    if (Errors.length > 0){

        console.log('appending errors');
        for(i=0;i<Errors.length;i++){

            ErrorList.innerHTML+=`<li>${Errors[i]}</li>`;
        }

        e.preventDefault();
    }
   

}


function clearErrors(){
    ErrorList.innerHTML='';
    SuccessMsg.innerHTML='';

}


