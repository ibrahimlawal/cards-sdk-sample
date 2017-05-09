<?php
if(($email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) 
    && ($amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT))){
    session_start();
    $_SESSION['email']=$email;
    $_SESSION['amount']=$amount;
    header('Location: payment');
    die();
}
?><form id="email-form" action="" method="post">
    <div id="error"><?php if(strtoupper($_SERVER['REQUEST_METHOD'])==='POST'){echo "Invalid details posted";}?></div>
    <input type="email" id="email" name="email" placeholder="email">
    <input type="number" id="amount" name="amount" placeholder="amount">
    <button type="submit" data-paystack="submit">Pay</button>
</form>

<style type="text/css">
    input{
        display:block;
    }
</style>

