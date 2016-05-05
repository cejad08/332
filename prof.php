<?php

/* Database config */

$db_host        = 'localhost';
$db_user        = 'root';
$db_pass        = '';
$db_database    = 'university'; 

/* End config */


#$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_database); 
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_database);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
echo '<a href="/index.php">Home</a> -
<a href="/stud.php">Student Interface</a> -
<a href="/prof.php">Professor Interface</a>';


?>

<html>
<head> <title>Professor Interface - CPSC 332 Project</title> </head>
<body>


</body>
</html>