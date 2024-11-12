<?php
    include("db.php");
    $q='';
    $cuisineId=0;
    $categoryId=0;

	if(isset($_GET['cuisineId'])) {
		$cuisineId=$_GET['cuisineId'];
	}
	if(isset($_GET['categoryId'])) {
		$categoryId=$_GET['categoryId'];
	}
	// check if category is selected
	if($categoryId !=0)
	   $q="select recipe_id,name,image from recipes WHERE categories_category_id=".$categoryId.
	   " order by name";	
	// check if cuisine is selected
	if($cuisineId !=0)
	   $q="select recipe_id,name,image from recipes WHERE cuisines_cuisine_id=".$cuisineId.
	   " order by name";
	
	// check if both are  selected
	if($cuisineId !=0 && $categoryId !=0)
	   $q="select recipe_id,name,image from recipes WHERE cuisines_cuisine_id=".$cuisineId.
	   " AND cuisines_cuisine_id=".$cuisineId." order by name";
	
	//all cuisine all category query	
	if($cuisineId ==0 && $categoryId ==0)	
	   $q="select recipe_id,name,image from recipes order by name";
    $result=mysqli_query($con, $q)or die(mysqli_error($con));
    $n=0;
    $s='<table align="center" dir="ltr"><tr >';
    while($row=mysqli_fetch_array($result)) {
        $pic=$row[2];
        if($n>3) {
            $s.='</tr><tr>';$n=0;
        }
        $s.=' <td align="center" id="foodName" style="padding:10px;"><div class="recipeImg"  onClick="window.location=\'recipeDetails.php?recipeId='.$row[0].'\';">  <img  style="padding-bottom:5px;" width=160  src="image/'.$pic.'"/><br><a style="text-decoration:none;" href="recipeDetails.php?recipeId='.$row[0].'"/>'.$row[1].'</div></td>';
        $n++;
    }
    $s.='</tr></table>';
    //cuisine
    $s1='<h2 style="padding-left:50px">Cuisine: </h2><br><table align="center"><tr>';
    $q1="select * from cuisines order by cuisine_name";
    $result1=mysqli_query($con, $q1)or die(mysqli_error($con));
     while($row1=mysqli_fetch_array($result1)) {
        $s1.=' <td><div id="c'.$row1[0].'" onclick="cuisineFn('.$row1[0].')" class="divBtn2" align="center">'.$row1[1].'</div></td>';
    }
    $s1.='<td><div id="c0" onclick="cuisineFn(0)" class="divBtn2" align="center">All Cuisines</div></td></tr></table>';

    //categories
    $s2='<h2 style="padding-left:50px">Category: </h2><br><table align="center"><tr>';
    $q2="select * from categories order by category_name";
    $result2=mysqli_query($con, $q2)or die(mysqli_error($con));
    while($row2=mysqli_fetch_array($result2)) {
        $s2.=' <td><div id="t'.$row2[0].'" onclick="categoryFn('.$row2[0].')" class="divBtn2" align="center">'.$row2[1].'</div></td>';
    }
    $s2.='<td><div id="t0" onclick="categoryFn(0)" class="divBtn2" align="center">All Categories</div></td></tr></table>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>My Recipes</title>
    <link rel="icon" href="image/icon32.ico" type="image/x-icon" />
    <link href="styles.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
        var selectedCuisineId = "c" + <?php echo $cuisineId ?>;
        var selectedCategoryId = "t" + <?php echo $categoryId ?>;
        //alert(selectedCuisineId);
        function onLoad() {
            document.getElementById(selectedCuisineId).style.backgroundColor = "gray";
            document.getElementById(selectedCategoryId).style.backgroundColor = "gray";
        }
        function resetBgClrOfOtherCuisineBtns() {
            for (i = 0; i <= 5; i++) {
                var id = "c".concat(i.toString());

                document.getElementById(id).style.backgroundColor = "#f886a5";//"#f886a5"
            }
        }
        function resetBgClrOfOtherCategoryBtns() {
            for (i = 0; i <= 5; i++) {
                var id = "t".concat(i.toString());

                document.getElementById(id).style.backgroundColor = "#f886a5";//f886a5
            }
        }
        function cuisineFn(x) {
            //change the selected button background color
            resetBgClrOfOtherCuisineBtns();
            var id = "c".concat(x.toString());
            document.getElementById(id).style.backgroundColor = "gray";

            if (x != 0) {
                window.location = "index.php?cuisineId=" + x;

            } else
                window.location = "index.php";
        }

        function categoryFn(x) {

            //change the selected button background color
            resetBgClrOfOtherCategoryBtns();
            var id = "t".concat(x.toString());
            document.getElementById(id).style.backgroundColor = "gray";

            if (x != 0) {
                window.location = "index.php?categoryId=" + x;

            } else
                window.location = "index.php";
        }
    </script>
</head>
<body onload="onLoad()"></body>
    <div id="login-link" style="text-align: right; padding: 10px;">
        <a href="./login.php">Login</a>
    </div>
<body onload="onLoad()">
    <div id="bg_top">
        <div id="wrapper">
            <div id="header">
             <h1>Oakland Recipe Board</h1>
            </div>
            <!--end header-->
            <div id="content_bg" style="width:990px">
                <!--start main content area-->
                <div id="content" style="width:920px">
                    <table width="900" align="center">
                        <tr align="center">
                            <td> </td>
                        </tr>
                    </table>
                    <?php echo $s1; ?>
                    <br />
                    <?php echo $s2; ?>
                    <br />
                    <?php  echo $s; ?>
                    <br />
                    <br />
                    <h1><br class="clear" />
                    </h1>
                </div><br />
                
                <div id="footer">
                </div>
                <!--end footer-->
            </div>
            <!--end wrapper-->
        </div>
        <!--end bg_top-->
        <div id="grass">
            <div id="footer_design"></div>
        </div>
        <!--end grass-->
        <!--<script type="text/javascript"> Cufon.now(); </script>-->

        
        </body>

</html>