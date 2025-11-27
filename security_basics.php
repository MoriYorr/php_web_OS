<?php
function validateEmail(string $email): ?string{
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        return $email;
    }
    return null;
}

function validateName(string $name): ?string{
    
}
?>