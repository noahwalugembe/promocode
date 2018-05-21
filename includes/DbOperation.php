<?php


//

// Helper method to get a string description for an HTTP status code
// From http://www.gen-x-design.com/archives/create-a-rest-api-with-php/ 
function getStatusCodeMessage($status)
{
    // these could be stored in a .ini file and loaded
    // via parse_ini_file()... however, this will suffice
    // for an example
    $codes = Array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    return (isset($codes[$status])) ? $codes[$status] : '';
}

// Helper method to send a HTTP response code/message
function sendResponse($status = 200, $body = '', $content_type = 'text/html')
{
    $status_header = 'HTTP/1.1 ' . $status . ' ' . getStatusCodeMessage($status);
    header($status_header);
    header('Content-type: ' . $content_type);
    echo $body;
}


class DbOperation
{
    //Database connection link
    private $con;
 
    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';
 
        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();
 
        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }
	
	/*
	* The create operation
	* When this method is called a new record is created in the database //'event_lat','event_long'
	*/
	function createCode($code,$unlock_code,$uses_remaining,$amount,$expire_date,$active,$radius,$event_lat,$event_long){
		$stmt = $this->con->prepare("INSERT INTO rw_promo_code (code,unlock_code,uses_remaining,amount,expire_date,active,radius,origin_lat,origin_long) VALUES ('$code','$unlock_code','$uses_remaining','$amount','$expire_date','$active','$radius','$event_lat','$event_long')");
		//$stmt->bind_param("ssis", $name, $realname, $rating, $teamaffiliation);
		if($stmt->execute())
			return true; 
		return false; 
	}

	/*
	* The read operation
	* When this method is called it is returning all the existing record of the database
	*/
	
	function getCode(){
		$stmt = $this->con->prepare("SELECT id, code,unlock_code,uses_remaining,amount,expire_date,active,radius FROM rw_promo_code");
		$stmt->execute();
		$stmt->bind_result($id, $code,$unlock_code,$uses_remaining,$amount,$expire_date,$active,$radius);
		
		$heroes = array(); 
		
		while($stmt->fetch()){
			$hero  = array();
			$hero['id'] = $id; 
			$hero['code'] = $code; 
			$hero['unlock_code'] = $unlock_code; 
			$hero['amount'] = $amount; 
			
            $hero['expire_date'] = $expire_date; 
			$hero['active'] = $active; 
			$hero['radius'] = $radius; 
			 			
			
			array_push($heroes, $hero); 
		}
		
		return $heroes; 
	}
	
	
	function getActive($active){
		$stmt = $this->con->prepare("SELECT id, unlock_code,uses_remaining,amount,expire_date,active,radius FROM rw_promo_code WHERE active=$active");
		$stmt->execute();
		$stmt->bind_result($id, $unlock_code,$uses_remaining,$amount,$expire_date,$active,$radius);
		
		$heroes = array(); 
		
		while($stmt->fetch()){
			$hero  = array();
			$hero['id'] = $id; 
			$hero['unlock_code'] = $unlock_code; 
			$hero['uses_remaining'] = $uses_remaining; 
			$hero['amount'] = $amount; 
			
			$hero['expire_date'] = $expire_date; 
			$hero['active'] = $active; 
			$hero['radius'] = $radius; 
			 
			
			array_push($heroes, $hero); 
		}
		
		return $heroes; 
	}
	
	/*
	* The update operation
	* When this method is called the record with the given id is updated with the new given values
	*/            //array('unlock_code','uses_remaining','amount','expire_date','active','radius','id'
	function updateCode($code,$unlock_code,$uses_remaining,$amount,$expire_date,$active,$radius,$id){
		$stmt = $this->con->prepare("UPDATE rw_promo_code SET code = '$code', unlock_code = '$unlock_code', uses_remaining = '$uses_remaining', amount = '$amount', expire_date = '$expire_date', active = '$active', radius = '$radius' WHERE id = '$id'");
		
		if($stmt->execute())
			return true; 
		return false; 
	}
	
	
	/*
	* The delete operation
	* When this method is called record is deleted for the given id 
	*/
	function deleteHero($id){
		$stmt = $this->con->prepare("DELETE FROM rw_promo_code WHERE id = ? ");
		$stmt->bind_param("i", $id);
		if($stmt->execute())
			return true; 
		
		return false; 
	}
	
	/*
	* The delete operation
	* When this method is called record is deleted for the given id 
	*/
	
	    // Main method to redeem a code
   function redeemCode($rw_app_idx,$codex,$device_idx,$pickup_latx,$pickup_longx,$hav_distance) {

    // Check for required parameters
    if (isset($rw_app_idx) && isset($codex) && isset($device_idx)) {
    
        // Put parameters into local variables
        $rw_app_id = $rw_app_idx;
        $code = $codex;
        $device_id = $device_idx;
        $active_code=1;
		$pickup_lat=$pickup_latx;
		$pickup_long=$pickup_longx;
        // Look up code in database
        $user_id = 0;
        $stmt = $this->con->prepare("SELECT id, unlock_code, uses_remaining,active,radius,origin_lat, origin_long FROM rw_promo_code WHERE code='$code' AND active='$active_code'");
        //$stmt->bind_param("is", $rw_app_id, $code);
        $stmt->execute();
        $stmt->bind_result($id, $unlock_code, $uses_remaining ,$active,$radius,$origin_lat, $origin_long);
        while ($stmt->fetch()) {
            break;
        }
        $stmt->close();
        
       
	
		// Bail if code doesn't exist
        if ($id <= 0) {
            sendResponse(400, 'Invalid code');
            return false;
        }
        
        // Bail if code already used		
        if ($uses_remaining <= 0) {
            sendResponse(403, 'Code already used');
            return false;
        }	
        
        // Check to see if this device already redeemed	
        $stmt = $this->con->prepare('SELECT id FROM rw_promo_code_redeemed WHERE device_id=? AND rw_promo_code_id=?');
        $stmt->bind_param("si", $device_id, $id);
        $stmt->execute();
        $stmt->bind_result($redeemed_id);
        while ($stmt->fetch()) {
            break;
        }
        $stmt->close();
        
        // Bail if code already redeemed
        if ($redeemed_id > 0) {
            sendResponse(403, 'Code already used');
            return false;
        }
		//
 		

		
        // Add tracking of redemption
        $stmt = $this->con->prepare("INSERT INTO rw_promo_code_redeemed (rw_promo_code_id, device_id) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $device_id);
		//$stmt->bind_param("is", $id,$hav_distance);
        $stmt->execute();
        $stmt->close();
        
        // Decrement use of code
        $this->con->query("UPDATE rw_promo_code SET uses_remaining=uses_remaining-1 WHERE id=$id");
        $this->con->commit();
        
        // Return unlock code, encoded with JSON
        $result = array(
            "unlock_code" => $unlock_code,
        );
        sendResponse(200, json_encode($result));
        return true;
    }
    sendResponse(400, 'Invalid request');
    return false;

}

// Main method to validate a code
   function validateCode($codex,$pickup_latx,$pickup_longx,$hav_distance,$encodedString) {

    // Check for required parameters
    if (isset($codex) && isset($pickup_latx) && isset($pickup_longx)) {
    
        // Put parameters into local variables
        
        $code = $codex;
        $active_code=1;
		$pickup_lat=$pickup_latx;
		$pickup_long=$pickup_longx;
        // Look up code in database
        $user_id = 0;
        $stmt = $this->con->prepare("SELECT id, unlock_code, uses_remaining,active,radius,origin_lat, origin_long FROM rw_promo_code WHERE code='$code' AND active='$active_code'");
        //$stmt->bind_param("is", $rw_app_id, $code);
        $stmt->execute();
        $stmt->bind_result($id, $unlock_code, $uses_remaining ,$active,$radius,$origin_lat, $origin_long);
        while ($stmt->fetch()) {
            break;
        }
        $stmt->close();
        
       
	
		// Bail if code doesn't exist
        if ($id <= 0) {
            sendResponse(400, 'Invalid code');
            return false;
        }
        
        // Bail if code already used		
        if ($uses_remaining <= 0) {
            sendResponse(403, 'Code  used up');
            return false;
        }	
        	
		 
		  // Return unlock code, encoded with JSON
        $result = array(
            "unlock_code" => $unlock_code,
			"uses_remaining" => $uses_remaining,
			"active" => $active,
			"radius" => $radius,
			"origin_lat" => $origin_lat,
			"origin_long" =>$origin_long,
			"pickup_lat" => $pickup_lat,
			"pickup_long" => $pickup_long,
			"polyline" => $encodedString
			
        );
        sendResponse(200, json_encode($result));
        return true;
    }
    sendResponse(400, 'Invalid request');
    return false;

}

	
	
	
}