<!DOCTYPE html>
 
<html>
<head>
    <title>EenmaalAndermaal > Sign up</title>
    <link href="verification.css" type="text/css" rel="stylesheet" />
</head>
<body>
    <!-- start header div -->
    <div id="header">
        <h3>EenmaalAndermaal > Sign up</h3>
    </div>
    <!-- end header div -->  
     
    <!-- start wrap div -->  
    <div id="wrap">
         
        <!-- start php code -->
        <?php
 
            if(isset($_POST['name']) && !empty($_POST['name']) AND isset($_POST['email']) && !empty($_POST['email'])){
            // Form Submited
        }
            if(  ){ // If statement is true run code between brackets
 
            }
 
            isset($_POST['name']) // Is the name field being posted; it does not matter whether it's empty or filled.
            && // This is the same as the AND in our statement; it allows you to check multiple statements.
            !empty($_POST['name']) // Verify if the field name is not empty
 
            isset($_POST['email']) // Is the email field being posted; it does not matter if it's empty or filled.
            && // This is the same as the AND in our statement; it allows you to check multiple statements.
            !empty($_POST['email']) // Verify if the field email is not empty
            
            if(isset($_POST['name']) && !empty($_POST['name']) AND isset($_POST['email']) && !empty($_POST['email'])){
                $name = $_POST['name']; // Turn our post into a local variable
                $email = $_POST['email']; // Turn our post into a local variable
                
                if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
                    // Return Error - Invalid Email
                    $msg = 'The email you have entered is invalid, please try again.';
                }else{
                    // Return Success - Valid Email
                    $msg = 'Your account has been made, <br /> please verify it by clicking the activation link that has been send to your email.';
                }
                }
        ?>
        <!-- stop php code -->
     
        <!-- title and description -->   
        <h3>Signup Form</h3>
        <p>Please enter your name and email addres to create your account</p>
        
        <?php 
            if(isset($msg)){  // Check if $msg is not empty
            echo '<div class="statusmsg">'.$msg.'</div>'; // Display our message and wrap it with a div with the class "statusmsg".
            } 
        ?>
        
        <!-- start sign up form -->  
        <form action="" method="post">
            <label for="name">Name:</label>
            <input type="text" name="name" value="" />
            <label for="email">Email:</label>
            <input type="text" name="email" value="" />
             
            <input type="submit" class="submit_button" value="Sign up" />
        </form>
        <!-- end sign up form -->
         
    </div>
    <!-- end wrap div -->
</body>
</html>