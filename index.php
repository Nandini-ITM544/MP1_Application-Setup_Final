<?php session_start(); ?>
<html>
<head><title>Hello app</title>
<meta charset="utf-8">
</head>
<body>


<form enctype="multipart/form-data" action="result.php" method="POST">
    
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
   
    Send this file: <input name="userfile" type="file" /><br />
<br>
<br>
Enter Email of user: <input type="email" name="useremail"><br />
<br>
<br>
Enter Phone of user (1-XXX-XXX-XXXX): <input type="phone" name="phone">
<br>
<br>

<input type="submit" value="Submit Details" />
</form>





</body>
</html>