function validate() {

    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    var letters = /^[A-Za-z\s]+$/;
    var alfanumeric = /^[0-9a-zA-Z]+$/;

    // Check email format
    if (!(document.myForm.email.value.match(mailformat)))  {
        alert("Email is not valid format");
        document.myForm.email.focus();
        return false;
    }

    // Check Name format
    if(!(document.myForm.name.value.match(letters))) {
        alert("Name format invalid");
        document.myForm.name.focus();
        return false;
    }

    // Check Surname format
    if(!(document.myForm.surname.value.match(letters))) {
        alert("Surname format invalid");
        document.myForm.surname.focus();
        return false;
    }

}
