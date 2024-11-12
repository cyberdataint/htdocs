<?php
	$con = mysqli_connect("localhost","root","root") or die(mysqli_error($con));
    mysqli_query($con,"SET NAMES 'utf8'"); 
    mysqli_query($con,'SET CHARACTER SET utf8'); 
	$db = mysqli_select_db($con, "my_recipes") or die(mysqli_error($con));
 ?>
