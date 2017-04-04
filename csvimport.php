<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname=  "medican";
$conn = new mysqli($servername, $username, $password, $dbname);
$file_handle = fopen('DropdownWard1.csv', 'r');
$data = array();
$data  = fgetcsv($file_handle);
$i=1;
 while(! feof($file_handle))
    {
        $data = array();
        $data  = fgetcsv($file_handle);
        $news[] = $data;
        echo'<pre>'; print_r($data);
        $Province = $data[0];
        $Code1 =   $data[1];
        $District = $data[2];
        $Code2 =  $data[3];
        $Ward =  $data[4];
        $Code3 = $data[5];
        $Code4 =  $data[6];

        $Code11 = sprintf("%02d", $Code1);
        $Code22 = sprintf("%03d", $Code2);
        $Code33 = sprintf("%05d", $Code3);
        $sql = "INSERT INTO wp_store_address (Province, District, Ward, Code1, Code2, Code3, Code4)
        VALUES ('$Province', '$District', '$Ward','$Code11','$Code22','$Code33','$Code4')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
            echo '<br>'.$i;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

$i++;
   }
 

