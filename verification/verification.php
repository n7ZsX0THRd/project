<?php

if(isset($_POST['register']))
{
	$email_id=$_POST['email'];
	$pass=$_POST['password'];
	$code= rand(100000,999999);
//	mysql_connect('localhost','root','');
//	mysql_select_db('sample');
//	
//	$insert=mysql_query("insert into verify values('','$email','$pass','$code')");
//	$db_id=mysql_insert_id();

	$message = "Your Activation Code is ".$code."";
    $to=$email_id;
    $subject="Activation Code For EenmaalAndermaal";
    $from = 'noreply@iproject2.icasites.nl';
    $body='Your Activation Code is '.$code.' Please Click On This link http://iproject2.icasites.nl to activate your account.';
    $headers = "From:".$from;
    mail($to,$subject,$body,$headers);
	
	echo "An Activation Code Is Sent To You, Check Your Emails";
}

//if(isset($_GET['id']) && isset($_GET['code']))
//{
//	$id=$_GET['id'];
//	$code=$_GET['id'];
//	mysql_connect('localhost','root','');
//	mysql_select_db('sample');
//	$select=mysql_query("select email,password from verify where id='$id' and code='$code'");
//	if(mysql_num_rows($select)==1)
//	{
//		while($row=mysql_fetch_array($select))
//		{
//			$email=$row['email'];
//			$password=$row['password'];
//		}
//		$insert_user=mysql_query("insert into verified_user values('','$email','$password')");
//		$delete=mysql_query("delete from verify where id='$id' and code='$code'");
//	}
//}

?>