



<?php
$sqli_conn = new mysqli("project1db.c7brzs9x3acd.us-west-2.rds.amazonaws.com","nandini","nandinipwd","Project1db",3306) or die("Error " . mysqli_error($link)); 

$res = $sqli_conn->query("CREATE TABLE Projectrec
(
id int NOT NULL AUTO_INCREMENT,
uname VARCHAR(20),
email VARCHAR(20),
phone VARCHAR(20),
raws3url VARCHAR(256),
finisheds3url VARCHAR(256),
jpegfilename VARCHAR(256),
state tinyint(3) CHECK(state IN (0,1,2)),
DateTime timestamp,
PRIMARY KEY (id)
)");
$permission=shell_exec("chmod 600 setup.php");
?>
