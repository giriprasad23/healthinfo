<html>
<script type="text/javascript" src="fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
<style>

.button1 {
 
    font-family: Verdana, Geneva, sans-serif;
    font-size: 24px;
    color: #FFF;
    padding: 5px 10px 5px 10px;
    border: solid 1px #CCC;
 
    background: #ba4742;
    text-shadow: 0px 1px 0px #000;
 
    border-radius: 5px;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
 
    box-shadow: 0 1px 3px #111;
    -moz-box-shadow: 3px 3px 1px #999;
    -webkit-box-shadow: 3px 3px 1px #999;
 
    cursor: pointer;
 
}
.button1:hover {
    background: #a33f3a;
}

table, th, td,tr {
    border: 1px black;
	padding=2px;
	
}
h1 {
   margin: 1em 0 0.5em 0;
	font-weight: 600;
	font-family: times, Times New Roman, times-roman, georgia, serif;
	color: #444;
	position: relative;  
	font-size: 60px;
	line-height: 90px;
	padding: 15px 15px 15px 15%;
	
	box-shadow: 
		inset 0 0 0 1px rgba(53,86,129, 0.4), 
		inset 0 0 5px rgba(53,86,129, 0.5),
		inset -285px 0 35px white;
	border-radius: 0 15px 0 5px;
	background: #fff url(images/heartbeat.jpg) no-repeat center left;
    padding:10px;
    margin:30px;
}

</style>
<head>
<link rel="stylesheet" type="text/css" href="css/form.css">
</head>
<body >
<div class="register-form">
<?php
	error_reporting(E_ALL);
	ini_set('display_errors','On');
	if(isset($msg) & !empty($msg)){
		echo $msg;
	}
 ?>
<h1 align='center'>Hospital Navigation System</h1>
<link rel='stylesheet' type='text/css' href='bootstrap.css' /> 
<form action="hospital.php" method="POST" >
<table id="table1"; cellspacing="5px" cellpadding="5%"; align="center">
	 <tr><td  align="right"><p><label><b>Admission Diagnosis Code<font color="red">*</font> : </b></label> </td>
	 <td><input id="diag_code" type="text" name="diagnosis" placeholder="Diagnosis code" required /></p></td></tr>
    <tr><td  align="right"><p><label><b>Patient Age Group :</b></label> </td>
	<td><select name="age_group">
		<option value="">Select age</option>
		<option value="1">Less than 25</option>
		<option value="2">25 - 44</option>
		<option value="3">45 - 64</option>
		<option value="4">65 - 69</option>
		<option value="5">70 - 74</option>
		<option value="6">75 - 79</option>
		<option value="7">80 - 84</option>
		<option value="8">85 - 89</option>
		<option value="9">90 and above</option>
	</select> </p> </td></tr>
 
    <tr><td  align="right">  <p><label><b>Patient Gender : </b>	</label></td>
	<td><select name="formGender">
		<option value="">Select Gender</option>
		<option value="1">Male</option>
		<option value="2">Female</option>
		<option value="0">Unknown</option>
	</select></p> </td></tr>
	<tr><td  align="right"><p><label><b>Patient Ethnicity :</b></label> </td>
	<td><select name="ethnicity">
		<option value="">Select ethnicity</option>
		<option value="1">White</option>
		<option value="2">Black</option>
		<option value="3">Other</option>
		<option value="4">Asian</option>
		<option value="5">Hispanic</option>
		<option value="6">North American Native</option>
		<option value="0">Unknown</option>
	</select> </p> </td></tr>
	<tr><td align="right"><p><label><b>What are you looking for? <font color="red">*</b></label> </td>
	<td> <input type="radio" name="whatyouneed" value="AVERAGE_COST" required >Avg. Cost</input>
	<input type="radio" name="whatyouneed" value="LENGTH_OF_STAY" required >Length of Stay</input>
	</td></tr>
	</table>
   <p align="center"> <input type="submit" name="submit" value="Submit" class = "button1"/></p>
    </form>
	

</div>


<?php
include("includes/fusioncharts.php");
$ServerName = "localhost";
$UserName = "root";
$DBName = "healthdb";
$Password = "";
if(isset($_POST['submit']))
{
$Age ="";
$Diagnosis_Code="";
$Sex ="";
	$Diagnosis_Code = $_POST["diagnosis"];
	$Age = $_POST["age_group"];
	$Sex = $_POST["formGender"];
	$ethnicity=$_POST["ethnicity"];
	$whatyouneed=$_POST["whatyouneed"];
	
	$conn = new mysqli($ServerName, $UserName,$Password, $DBName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$whereCondition = " ";
if(!empty($Age))
{
	$whereCondition = $whereCondition . "AND AGE = " . $Age . " ";
}
if(!empty($Sex))
{
	$whereCondition = $whereCondition . "AND sex = " . $Sex . " ";
}
if(!empty($ethnicity))
{
	$whereCondition = $whereCondition . "AND race = " . $ethnicity . " ";
}

$sql = "select a.NATIONAL_PROVIDER_ID as NATIONAL_PROVIDER_ID, ceil(avg(a.LENGTH_OF_STAY)) as LENGTH_OF_STAY, ceil(avg(a.TOTAL_CHARGES)) as AVERAGE_COST
from health_data a where a.ADMITTING_DIAGNOSIS_CODE = ". $Diagnosis_Code . $whereCondition ."
group by a.NATIONAL_PROVIDER_ID ORDER BY " . $whatyouneed . " ASC;";

if($whatyouneed == 'LENGTH_OF_STAY'){
	$chartvalue = "LENGTH_OF_STAY";
	$subcaptiony = "Length of Stay";
	$numberprefix = "";
	$numbersuffix="Days";
 }
else 
{
	$chartvalue="AVERAGE_COST";
	$subcaptiony = "Average cost";
	$numberprefix="$";
	$numbersuffix="";
}
$result = $conn->query($sql);
	$arrData = array(
  "chart" => array
  (
    "caption" => "National Providers v/s " . $subcaptiony,
	"xaxisname"=> "National Provider",
	"yaxisname"=> $subcaptiony,
    "paletteColors" => "#0075c2",
    "bgColor" => "#ffffff",
    "borderAlpha"=> "20",
    "canvasBorderAlpha"=> "0",
    "usePlotGradientColor"=> "0",
    "plotBorderAlpha"=> "10",
    "showXAxisLine"=> "1",
    "xAxisLineColor" => "#999999",
    "showValues" => "0",
    "divlineColor" => "#999999",
    "divLineIsDashed" => "1",
    "showAlternateHGridColor" => "0",
	"numberprefix"=>$numberprefix,
	"numbersuffix"=>$numbersuffix
  )
);
$arrData["data"] = array();

if ($result->num_rows > 0) {
	echo "<table border = \"1\" align=\"left\" style=\"width:40%\"><tr><th>NATIONAL PROVIDER</th><th>LENGTH OF STAY</th><th>AVG. COST</th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
		echo '<tr><td><a href="person.php?NATIONAL_PROVIDER_ID='.$row['NATIONAL_PROVIDER_ID'] .'&amp;ADMITTING_DIAGNOSIS_CODE='. $Diagnosis_Code .'">'.$row['NATIONAL_PROVIDER_ID'].'</a></td><td>'.$row["LENGTH_OF_STAY"].'</td><td>$'.$row["AVERAGE_COST"].'</td></tr>';
		array_push($arrData["data"], array(
		"label" => $row["NATIONAL_PROVIDER_ID"],
		"value" => $row[$chartvalue]
  )
);
	}
	echo "</table>";
}
else
	echo "NO Results";
 

$jsonEncodedData = json_encode($arrData);
$columnChart = new FusionCharts("column3d", "myFirstChart" , 700, 600, "chart-1", "json", $jsonEncodedData);

$columnChart->render();
$conn->close();


}
?>
<div id="chart-1" align="right"><!-- Fusion Charts will render here--></div>
</body>
<footer>
<p align="center" style="font-size:15px"><a href='index.html'>HOME</a></p>
</html>

