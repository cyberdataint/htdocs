<?php
include("db.php");
if(isset($_GET['recipeId']))
$recipeId=$_GET['recipeId'];

$q0="select AVG(rating) from comments where recipes_recipe_id=".$recipeId.";";
 $result0=mysqli_query($con,$q0)or die(mysqli_error($con));
   $row0=mysqli_fetch_array($result0);
   switch ($row0[0]){
   case ($row0[0]<2): $ratingImg="stars1.png"; break;
   case ($row0[0]<3): $ratingImg="stars2.png"; break;
   case ($row0[0]<4): $ratingImg="stars3.png"; break;
   case ($row0[0]<5): $ratingImg="stars4.png"; break;
   case ($row0[0]<6): $ratingImg="stars5.png"; break;
   }
   
 $q="select * From recipes where recipe_id=".$recipeId;
 $result=mysqli_query($con,$q)or die(mysqli_error($con));
   $row=mysqli_fetch_array($result);
 
 $s='<table align="center" dir="ltr">';
 $s.='<tr > <td align="left" id="foodName" colspan=2 style="padding:10px; "><h1>'.$row[1].'</h1></td></tr>
 <tr><td > <img  style="padding-bottom:5px;width:600px"   src="image/'.$row[2].'"/></td>
 <td style="vertical-align: text-top; padding-left:20px"><h3>Rating: '.$row0[0].'<img src="image/'.$ratingImg.'"/></h3><h3>Preparation Time</h3><h2>'.$row[4].'<h2><br> <h2>Ingredients</h2><p>'.$row[3].'</p></td></tr><tr ><td colspan=2 style="vertical-align: text-top;"><h2>Preparation</h2></td></tr>';
 
 $q2="SELECT * FROM preparation_steps where recipes_recipe_id=".$recipeId;
 $result2=mysqli_query($con,$q2)or die(mysqli_error($con));
 while($row2=mysqli_fetch_array($result2)){
   
   $s.='<tr ><td style="vertical-align:top;"><p>'.$row2[1].'</p></td><td style="float:right"> <img  width=180 src="image/'.$row2[2].'"/><br> <br> </td></tr>';
   }
 $s.='</table>';

//comments on each recipe
$ss='<h2>Visitors Comments On This Recipe:</h2>';
$ss.='<table>'; 
$q3="SELECT * FROM comments c JOIN users u ON c.users_user_id= u.user_id where recipes_recipe_id=".$recipeId;
 $result3=mysqli_query($con,$q3)or die(mysqli_error($con));
 while($row3=mysqli_fetch_array($result3)){
   //$row3[1]=comment
   //$row3[2]=rating
   //$row3[3]=comment date
   //$row3[7]= user name
   
   switch ($row3[2]){
   case ($row3[2]<2): $ratingImg="stars1.png"; break;
   case ($row3[2]<3): $ratingImg="stars2.png"; break;
   case ($row3[2]<4): $ratingImg="stars3.png"; break;
   case ($row3[2]<5): $ratingImg="stars4.png"; break;
   case ($row3[2]<6): $ratingImg="stars5.png"; break;
   }
   $date = new DateTime($row3[3]);

   $ss.='<tr style="vertical-align:top;"><td  style="color:green"><p>'.$row3[7].': <span style="color: gray">'.$row3[1].'</span></P>
   <span style=" color:gray">'.$date->format('Y-m-d H:i:s').'</span>
   <br> <img  height="20" src="image/'.$ratingImg.'"/> </td></tr>'; 
   }

$ss.= ' </table>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html   xmlns="http://www.w3.org/1999/xhtml" >
<head>
<style>
p{
font-size:16px;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>My Recipes</title>
<link rel="icon" href="image/icon32.ico" type="image/x-icon" />
<link rel="image_src"  type="image/jpeg" href="images/icon32.png"  />
<link href="styles.css" rel="stylesheet" type="text/css" />
</head>
<body  >
<div id="bg_top">
  <div id="wrapper">
    <div id="header">
      <div id="logo"><img src="image/myRecipes.png" />
      <div style="margin-left: 280%;">
      <div onclick="window.location='index.php'" class="divBtn2" align="center">All Recipes</div>
      </div>
      </div> 
      </div>
    <!--end header-->
    <div id="content_bg" >
      <!--start main content area-->
      <div id="content" style="width:920px">
       <table width="900" align="center">
  <tr align="center">
    <td> </td>
  </tr>
</table>
        <?php echo $s; ?>
        <br />
        <?php  echo $ss; ?>
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
    </div>
</body>
</html>
