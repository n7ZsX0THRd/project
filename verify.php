<?php
if(isset($_GET['r_username']) && !empty($_GET['r_username']) && isset($_GET['code']) && !empty($_GET['code'])){
    // Verify data
    $email = $_GET['r_username'];
    $code = $_GET['code'];
}else{
    // Invalid approach
        echo 'Invalid approach, please use the link that has been send to your email.';
        $code = rand(100000,999999);
        echo $code;

}
?>