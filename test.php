<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "connectova",
    3310
);

if($conn){
    echo "Database Connected Successfully!";
}
else{
    die(mysqli_connect_error());
}

?>