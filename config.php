<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "connectova",
    3306,
);

if(!$conn)
{
    die("Connection Failed: " . mysqli_connect_error());
}

?>