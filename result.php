<?php
// Start the session
session_start();
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
echo $_POST['useremail'];
$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$fname = $_FILES['userfile']['name'];
echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";
require 'vendor/autoload.php';

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
#print_r($s3);
$bucket = uniqid("nandinibuckettest",false);

echo $bucket;
# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
]);

# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => $fname,
'SourceFile' => $uploadfile,
]);  
$url = $result['ObjectURL'];
echo $url;
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'Project1db',
    ]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";

$link = mysqli_connect($endpoint,"nandini","nandinipwd","Project1db") or die("Error " . mysqli_error($link));
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
/* Prepared statement, stage 1: prepare */
if (!($stmt = $link->prepare("INSERT INTO Projectrec (uname, email, phone, raws3url, finisheds3url, jpegfilename, state, DateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
$uname = "MyName";
$email = $_POST['useremail'];
$phone = $_POST['phone'];
$raws3url = $url; 
$jpegfname = basename($fname);
$finisheds3url = "none";
$state = 0;
$DateTime=date("Y-m-d H:i:s");

$stmt->bind_param("sss", '$uname', '$email', '$phone', '$raws3url', '$finisheds3url', '$jpegfilename', $state, '$DateTime');

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);
/* explicit close recommended */
$stmt->close();
$link->real_query("SELECT * FROM Projectrec");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo $row['id'] . " " . $row['email']. " " . $row['phone'];
}
$link->close();
//add code to detect if subscribed to SNS topic 
//if not subscribed then subscribe the user and UPDATE the column in the database with a new value 0 to 1 so that then each time you don't have to resubscribe them
// add code to generate SQS Message with a value of the ID returned from the most recent inserted piece of work
//  Add code to update database to UPDATE status column to 1 (in progress)
?>