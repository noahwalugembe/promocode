<?php 

	//getting the dboperation class
	require_once '../includes/DbOperation.php';

	//function validating all the paramters are available
	//we will pass the required parameters to this function 
	function isTheseParametersAvailable($params){
		//assuming all parameters are available 
		$available = true; 
		$missingparams = ""; 
		
		foreach($params as $param){
			if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
				$available = false; 
				$missingparams = $missingparams . ", " . $param; 
			}
		}
		
		//if parameters are missing 
		if(!$available){
			$response = array(); 
			$response['error'] = true; 
			$response['message'] = 'Parameters ' . substr($missingparams, 1, strlen($missingparams)) . ' missing';
			
			//displaying error
			echo json_encode($response);
			
			//stopping further execution
			die();
		}
	}
	//
	
	function promoCode($length){
    // POSSIBLE COMBINATIONS > HUNDREDS OF MILLIONS IF LENGTH > 6
    //           1...5...10...15...20...25...30.
    $alphabet = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
    $strlen   = strlen($alphabet);
    $string   = NULL;
    while(strlen($string) < $length){
        $random = mt_rand(0,$strlen);
        $string .= substr($alphabet, $random, 1);
        }
    return($string);
     }

   $code = promoCode(3);
   
   //Calculating distance 
   
function distance_haversine($lat1, $lon1, $lat2, $lon2) {
  global $earth_radius;
  global $delta_lat;
  global $delta_lon;
  $alpha    = $delta_lat/2;
  $beta     = $delta_lon/2;
  $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
  $c        = asin(min(1, sqrt($a)));
  $distance = 2*$earth_radius * $c;
  $distance = round($distance, 4);
 
  return $distance;
}
   
   
	
	//an array to display response
	$response = array();
	
	//if it is an api call 
	//that means a get parameter named api call is set in the URL 
	//and with this parameter we are concluding that it is an api call
	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){
			
			//the CREATE operation
			//if the api call value is 'createhero'
			//we will create a record in the database
			case 'createhero':
				//first check the parameters required for this request are available or not
                //unlock_code, uses_remaining	,'rating','teamaffiliation'			
				isTheseParametersAvailable(array('unlock_code','uses_remaining','amount','start_date','expire_date','active','radius'));
				
				//creating a new dboperation object
				$db = new DbOperation();
				
				//creating a new record in the database
				$result = $db->createHero(
					$code,
					$_POST['unlock_code'],
					$_POST['uses_remaining'],
					$_POST['amount'],
					$_POST['start_date'],
					$_POST['expire_date'],
					$_POST['active'],
					$_POST['radius']
					
				);
				//$_POST['rating'],
					//$_POST['teamaffiliation']

				//if the record is created adding success to response
				if($result){
					//record is created means there is no error
					$response['error'] = false; 

					//in message we have a success message
					$response['message'] = 'Hero addedd successfully';

					//and we are getting all the heroes from the database in the response
					$response['heroes'] = $db->getHeroes();
				}else{

					//if record is not added that means there is an error 
					$response['error'] = true; 

					//and we have the error message
					$response['message'] = 'Some error occurred please try again';
				}
				
			break; 
			
			//the READ operation
			//if the call is getheroes
			case 'getheroes':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['heroes'] = $db->getHeroes();
			break; 
			
			//RedeemAPI
			case 'redeemcode':
			    //first check the parameters required for this request are available or not
                //unlock_code, uses_remaining	,'rating','teamaffiliation'			
				isTheseParametersAvailable(array('rw_app_id','code','device_id','pickup_or_destination_lat','pickup_or_destination_long'));
				
				//Selecting radius and venue coordinates from the data abase 
				$promo_code=$_POST['code'];
				$dbhost = 'localhost';
                $dbuser = 'root';
                $dbpass = '';
		        $rec_limit = 2;
                $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
         
                if(! $conn ) {die('Could not connect: ' . mysql_error());}
                mysqli_select_db($conn,'SafeBoda_promo_code');
				
				$sql = "SELECT radius,origin_lat, origin_long, expire_date FROM rw_promo_code WHERE code='$promo_code' ";
                $result = mysqli_query($conn,$sql) or trigger_error("SQL", E_USER_ERROR);
                
                $num_rows = mysqli_num_rows($result);
				$radius;
				$origin_lat;
				$origin_long;
				$expire_date;
				
				
				if($num_rows > 0){
					
					while ( $db_field = mysqli_fetch_assoc($result) ) {
		 
                           $radius=$db_field['radius'];
                           $origin_lat=$db_field['origin_lat'];
						   $origin_long=$db_field['origin_long'];
						   $expire_date=$db_field['expire_date'];
					}
					
				}
            
             
				
				//Validating promo code radius
				
				$earth_radius = 3960.00; # in miles
                $lat_1 = $origin_lat;
                $lon_1 = $origin_long;
                
				$lat_2 = $_POST['pickup_or_destination_lat'];
                $lon_2 = $_POST['pickup_or_destination_long'];
                $delta_lat = $lat_2 - $lat_1 ;
                $delta_lon = $lon_2 - $lon_1 ;
				
				$hav_distance = distance_haversine($lat_1, $lon_1, $lat_2, $lon_2);
				
				
				
			//2015-12-31  y/m/d
				$todaydate = date('y/m/d' );
				
				
			if (($hav_distance <= $radius)&&($todaydate <= $expire_date)) {
				//creating a new dboperation object
				$db = new DbOperation();
				
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['heroes'] = $db->redeemCode(
					$_POST['rw_app_id'],
					$_POST['code'],
					$_POST['device_id'],
					$_POST['pickup_or_destination_lat'],
					$_POST['pickup_or_destination_long'],
					$hav_distance
					
					
					);
			} else{
				
				$response['message'] = 'The promo code is out of radius bounds or expired';
			}
				
			break; 
			
			case 'getactive':
			    //first check the parameters required for this request are available or not
                //unlock_code, uses_remaining	,'rating','teamaffiliation'			
				isTheseParametersAvailable(array('active'));
				
				//creating a new dboperation object
				$db = new DbOperation();
				
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['heroes'] = $db->getActive($_POST['active']);
				
				
			break; 
			
			
			
			
			//the UPDATE operation
			case 'updatehero':
				isTheseParametersAvailable(array('id','name','realname','rating','teamaffiliation'));
				$db = new DbOperation();
				$result = $db->updateHero(
					$_POST['id'],
					$_POST['name'],
					$_POST['realname'],
					$_POST['rating'],
					$_POST['teamaffiliation']
				);
				
				if($result){
					$response['error'] = false; 
					$response['message'] = 'Hero updated successfully';
					$response['heroes'] = $db->getHeroes();
				}else{
					$response['error'] = true; 
					$response['message'] = 'Some error occurred please try again';
				}
			break; 
			
			//the delete operation
			case 'deletehero':

				//for the delete operation we are getting a GET parameter from the url having the id of the record to be deleted
				if(isset($_GET['id'])){
					$db = new DbOperation();
					if($db->deleteHero($_GET['id'])){
						$response['error'] = false; 
						$response['message'] = 'Hero deleted successfully';
						$response['heroes'] = $db->getHeroes();
					}else{
						$response['error'] = true; 
						$response['message'] = 'Some error occurred please try again';
					}
				}else{
					$response['error'] = true; 
					$response['message'] = 'Nothing to delete, provide an id please';
				}
			break; 
		}
		
	}else{
		//if it is not api call 
		//pushing appropriate values to response array 
		$response['error'] = true; 
		$response['message'] = 'Invalid API Call';
	}
	
	//displaying the response in json structure 
	echo json_encode($response);
	
	
