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
            if(isset($_POST['name']) && !empty($_POST['name']) AND isset($_POST['email']) && !empty($_POST['email'])){
                $name = $_POST['name']; // Turn our post into a local variable
                $email = $_POST['email']; // Turn our post into a local variable
                
                if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
                    // Return Error - Invalid Email
                    $msg = 'The email you have entered is invalid, please try again.';
                }else{
                    // Return Success - Valid Email
                    $msg = 'Your account has been made, <br /> please verify it by clicking the activation link that has been send to your email.';
                    
                    $hash = md5(rand(0, 1000)); //random 32 character snippet.
                    
                    $password = rand(1000, 5000); //random nummer tussen 1000 en 5000. Bijvoorbeeld 4568.
                    
                    $to = $email; //Send email to user
                    $subject = 'Signup | Verification'; //Email subject
                    $message = '
                    
                    Thanks for Signing up!
                    
                    Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
                    
                    -----------------------
                    Username: '.$name.'
                    Password: '.$password.'
                    -----------------------
                    
                    Please click this link to activate your account:
                    http://www.iproject2.icasites.nl/verifaction/verify.php?email='.$email.'$hash='.$hash.'
                    
                    ';//Message including the link
                    
                    $headers = 'From:noreply@EenmaalAndermaal.nl' . "\r\n"; //Set form headers
                    mail($to, $subject, $message, $headers); //Send e-mail
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