function formValidation()  {  

    // Create variable passed by get 
    var username = document.registration.username;  
    var email = document.registration.email;  
    var name = document.registration.name;  
    var surname = document.registration.surname;  
    var password = document.registration.password;  
    
    if(username(username,6,20))  {  
        if(pass_validation(password,7,12))    {  
            if(allLetter(name,surname))    {  
                if(alphanumeric(username))  {    
                    if(ValidateEmail(email))   {   
                    }      
                }  
            }  
        }  
    }  
    return false;  
}  


// Check username
function username(username,max,min)  {  
    var username_len = username.value.length;  
    if (username_len == 0 || username_len >= max || username_len < min)  {  
        alert("User Id should not be empty / length be between "+min+" to "+max);  
        username.focus();  
        return false;  
    }  
    return true;  
}

// Check password Lenght
function pass_validation(password,max,min)  {  
    var password_len = password.value.length;  
    if (password_len == 0 ||password_len >= max || password_len < min)  {  
        alert("Password should not be empty, and length be between "+min+" to "+max);  
        password.focus();  
        return false;  
    }  
    return true;  
}  

// Check is field is alphebet only 
function allLetter(name,surname) {   
var letters = /^[a-zA-Z ]+$/;

    // Check name and surname field  
    if(name.value.match(letters) && surname.value.match(letters))  {  
        return true;  
    }  
    else  {  
        alert('Name and Surname must have alphabet characters only LOLOLO');  
        name.focus();
        surname.focus();  
        return false;  
    }  
} 

// Check is fiels is alphanumeric only 
function alphanumeric(username)  {   
    var letters = /^[0-9a-zA-Z]+$/;  

    // Check Username Format
    if(username.value.match(letters))  {  
        return true;  
    }  
    else {  
        alert('Username must have alphanumeric characters only');  
        uadd.focus();  
        return false;  
    }  
}  

// Check email correct field
function ValidateEmail(email)  {  
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
    if(email.value.match(mailformat))  {  
        return true;  
    }  
    else {  
        alert("You have entered an invalid email format address!");  
        uemail.focus();  
        return false;  
    }  
}

