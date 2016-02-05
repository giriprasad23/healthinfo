<?php
include("includes/fusioncharts.php");
$ServerName = "localhost";
$UserName = "root";
$DBName = "healthdb";
$Password = "";


	
	$conn = new mysqli($ServerName, $UserName,$Password, $DBName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

//$sql = "Select AGE,LEN_OF_STAY FROM tbl_name group by AGE";//WHERE HOSPITAL_ID = " . $Diagnosis_Code ;


$param1 = $_GET['NATIONAL_PROVIDER_ID'];
$param2 = $_GET['ADMITTING_DIAGNOSIS_CODE'];

$sql = "select NATIONAL_PROVIDER_ID,ADMITTING_DIAGNOSIS_CODE, ceil(avg(TOTAL_CHARGES)) as AVERAGE_COST ,ceil(avg(LENGTH_OF_STAY)) as AVERAGE_LENGTH_STAY, PROVIDER_CITY_NAME ,
((select count(b.DISCHARGE_STATUS)
	 from health_data b
	 where b.NATIONAL_PROVIDER_ID=" . $param1 . " and b.DISCHARGE_STATUS=1 and b.ADMITTING_DIAGNOSIS_CODE=" . $param2 . "  
	) 
/ 
	(select count(c.DISCHARGE_STATUS) 
     from health_data c
	 where c.NATIONAL_PROVIDER_ID=" . $param1 . " and c.ADMITTING_DIAGNOSIS_CODE=" . $param2 . " 
     )
) as MORTALITY_RATE
from health_data where national_provider_id=" . $param1 . " and admitting_diagnosis_code=" . $param2 . "";

$result = $conn->query($sql);
//$Chart=$conn->query($sql);
//echo $result->fetch_assoc();
//var NationaProvider, admittingDiagnosis, avgCost,cityNamephp,Mortality;
if ($result->num_rows > 0) {
	//echo "<table align=\"left\" padding=\"20px\"><tr><th>NATIONAL_PROVIDER_ID</th><th>ADMITTING_DIAGNOSIS_CODE</th><th>AVERAGE_COST</th><th>AVERAGE_LENGTH_STAY</th><th>PROVIDER_CITY_NAME</th><th>MORTALITY_RATE</th></tr>";
    // output data of each row
	echo "<div align=\"left\" style=\"background-color:#7B68EE; color:white; padding:20px; width:50%\">";
	echo "<h1><strong><i>Information of the requested National Provider</i></strong></h1>";
    while($row = $result->fetch_assoc()) {
		//echo "<tr><td>".$row["NATIONAL_PROVIDER_ID"]."</td><td>".$row["ADMITTING_DIAGNOSIS_CODE"]."</td><td>".$row["AVERAGE_COST"]."</td><td>".$row["AVERAGE_LENGTH_STAY"]."</td><td>".$row["PROVIDER_CITY_NAME"]."</td><td>". ceil($row["MORTALITY_RATE"]*100) ."%</td></tr>";
		echo "<ul style=\"list-style-type:none\">";
  echo "<p style=\"color:black\"><i>NATIONAL PROVIDER ID</p><li>".$row["NATIONAL_PROVIDER_ID"]."</i></li>";
  echo "<p style=\"color:black\"><i>ADMITTING DIAGNOSIS CODE</p><li>".$row["ADMITTING_DIAGNOSIS_CODE"]."</i></li>";
  echo "<p style=\"color:black\"><i>AVERAGE_COST</p><li>".$row["AVERAGE_COST"]."</i></li>";
  echo "<p style=\"color:black\"><i>PROVIDER CITY NAME</p><li>".$row["PROVIDER_CITY_NAME"]."</i></li>";
  $cityNamephp = $row["PROVIDER_CITY_NAME"];
  echo "<p style=\"color:black\"><i>MORTALITY RATE</p><li>".$row["MORTALITY_RATE"]."</i></li>";
  echo "</ul>"; 
	echo "</div>";
	}
	
	//echo "</table>";
}
else
	echo "NO Results";

 //echo "<div id=\"mapholder\"><script type=\"text/javascript\">showPosition();</script></div>";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <title>Live Demo of Google Maps Geocoding Example with PHP</title>
     
    <style>
    body{
        font-family:arial;
        font-size:.8em;
    }
     
    input[type=text]{
        padding:0.5em;
        width:20em;
    }
     
    input[type=submit]{
        padding:0.4em;
    }
     
    #gmap_canvas{
        width:100%;
        height:30em;
    }
     
    #map-label,
    #address-examples{
        margin:1em 0;
    }
    </style>
 
</head>
<body>
 
<?php
if($_POST){
 
    // get latitude, longitude and formatted address
    $data_arr = geocode($_POST['address']);
 
    // if able to geocode the address
    if($data_arr){
         
        $latitude = $data_arr[0];
        $longitude = $data_arr[1];
        $formatted_address = $data_arr[2];
                     
    ?>
 
    <!-- google map will be shown here -->
    <div id="gmap_canvas">Loading map...</div>
    <div id='map-label'>Map shows approximate location.</div>
 
    <!-- JavaScript to show google map -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>    
    <script type="text/javascript">
        function init_map() {
            var myOptions = {
                zoom: 14,
                center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
            });
            infowindow = new google.maps.InfoWindow({
                content: "<?php echo $formatted_address; ?>"
            });
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        google.maps.event.addDomListener(window, 'load', init_map);
    </script>
 
    <?php
 
    // if unable to geocode the address
    }else{
        echo "No map found.";
    }
}
?>
 
<div id='address-examples'>
    <div>Address examples:</div>
    <div>1. G/F Makati Cinema Square, Pasong Tamo, Makati City</div>
    <div>2. 80 E.Rodriguez Jr. Ave. Libis Quezon City</div>
</div>
 
<!-- enter any address -->
<form action="" method="post">
    <input type='text' name='address' placeholder='Enter any address here' />
    <input type='submit' value='Geocode!' />
</form>
 
<?php
 
// function to geocode address, it will return false if unable to geocode address
function geocode($address){
 
    // url encode the address
    $address = urlencode($address);
     
    // google map geocode api url
    $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}";
 
    // get the json response
    $resp_json = file_get_contents($url);
     
    // decode the json
    $resp = json_decode($resp_json, true);
 
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
             
            return $data_arr;
             
        }else{
            return false;
        }
         
    }else{
        return false;
    }
}
?>
 
</body>
</html>
