<?php //connect to database
    $conn = mysqli_connect('localhost','landlordsDB','landlords123','landlords');

    if(!$conn){
        echo 'Connection error:' . mysqli_connect_error();
    }
        //display error ?>