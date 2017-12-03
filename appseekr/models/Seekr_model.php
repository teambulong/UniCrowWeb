<?php
/*
curl https://api-uat.unionbankph.com/partners/sb/sandbox/v1/accounts \
  -H'accept: application/json' \
  -H 'content-type: application/json' \
  -H 'x-ibm-client-id: <YOUR CLIENT ID>' \
  -H 'x-ibm-client-secret: <YOUR CLIENT Secret>' \
  -X POST \
  -d '{
        "username":"jdelacruz",
        "password":"password",
        "account_name":"Juan Dela Cruz"
      }'

{
    "msg": "Test Account successfully created.",
    "data": {
        "user": {
            "username": "unicrow",
            "password": "pass123"
        },
        "account": {
            "account_number": "105428388368",
            "account_name": "UniCrow",
            "account_code": "42714",
            "account_type": "Savings Account",
            "status": "Active",
            "branch": "2283",
            "balance": "100000"
        }
    },
    "code": 200,
    "status": 1
}	  
*/
class Seekr_model extends CI_Model {
   
	public function __construct(){
		$this->load->database();
	}
	
	public function getParams(){
		return array(
			"x-ibm-client-id" => '68feeabd-a95e-4da9-b533-6cf87f80a4b4',
			"x-ibm-client-secret" => 'hE6bI2iJ3rB8rC1qG2nV5gK7tF8mB7bE8iT6jN0lL1jU6xJ1nA',
        );
	}
	
	public function getClientId(){
		return '68feeabd-a95e-4da9-b533-6cf87f80a4b4';
	}

	public function getClientSecret(){
		return 'hE6bI2iJ3rB8rC1qG2nV5gK7tF8mB7bE8iT6jN0lL1jU6xJ1nA';
	}

	public function postCURL($_url, $_param){
		/*
			curl 'https://api-uat.unionbankph.com/partners/sb/convergent/v1/oauth2/authorize?client_id=<YOUR CLIENT ID>&response_type=<REPLACE THIS VALUE>&scope=<REPLACE THIS VALUE>&redirect_uri=<REPLACE THIS VALUE>&state=<REPLACE THIS VALUE>' \
			  -H 'accept: text/html'
			  -X GET

			curl https://api-uat.unionbankph.com/partners/sb/convergent/v1/oauth2/authorize \
			  -H 'accept: text/html' \
			  -H 'content-type: application/x-www-form-urlencoded' \
			  -X POST \
			  -d'client_id=<YOUR CLIENT ID>&scope=<REPLACE THIS VALUE>&resource-owner=<REPLACE THIS VALUE>&redirect_uri=<REPLACE THIS VALUE>&original-url=<REPLACE THIS VALUE>'
		*/
        $postData = '';
        //create name value pairs seperated by &
        foreach($_param as $k => $v) 
        { 
          $postData .= $k . '='.$v.'&'; 
        }
        rtrim($postData, '&');


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    

        $output=curl_exec($ch);

        curl_close($ch);

        return $output;
    }

	public function getall(){   
		$this->db->select('*');
		$this->db->from('bulong_user');
		$this->db->order_by("id"); 
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return 0;
		}
	}

	public function getAtm()
	{
		/*
			curl https://api-uat.unionbankph.com/partners/sb/locators/v1/atms \
			  -H 'accept: application/json' \
			  -H 'x-ibm-client-id: <YOUR CLIENT ID>' \
			  -H 'x-ibm-client-secret: <YOUR CLIENT Secret>' \
			  -X GET
		*/
		$url = 'https://api-uat.unionbankph.com/partners/sb/locators/v1/atms';
        $postData = '';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				'accept: application/json',
				'x-ibm-client-id: ' . $this->getClientId(),
				'x-ibm-client-secret: ' . $this->getClientSecret(),
			)
		); 

        $output=curl_exec($ch);

        curl_close($ch);

        return $output;
	}
	
	public function getAccountNumber($to = ''){
		$accountNumber = '';
		if($to == 'unicrow'){
			$accountNumber = $this->uni_get_config('escrow_account_number');
		} elseif($to != '') {
			$accountNumber = $this->uni_get_userdetails($to,'account_no');; /*maangela1 @todo mp get from db */
		}
		return $accountNumber;
	}
	
    public function uni_get_config($key){
       $ci = &get_instance();
       $ci->load->database(); 
       
       if($key){
           $sql = 'SELECT * FROM config WHERE config_key = "'.$key.'"';
           $query = $ci->db->query($sql);
           
           $result = $query->result_array();
           if(count($result)){
               foreach($result as $row){
                   return $row['config_value'];
               }
           }
       }
    }

    public function uni_get_userdetails($username,$key){
       $ci = &get_instance();
       $ci->load->database(); 
       
       if($key){
           $sql = 'SELECT '.$key.' FROM users WHERE api_username = "'.$username.'"';
           $query = $ci->db->query($sql);
           
           $result = $query->result_array();
           if(count($result)){
               foreach($result as $row){
                   return $row[$key];
               }
           }
       }
    }

	public function sendpayment($from,$to,$amount,$details = null){
		/* get token */
		$url = 'https://api-uat.unionbankph.com/partners/sb/partners/v1/oauth2/token';
        $postData = "grant_type=password&client_id=".$this->getClientId()."&username=".$from[0]."&password=".$from[1]."&scope=Transfers";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				"content-type: application/x-www-form-urlencoded",
			)
		); 

        $output=curl_exec($ch);

        curl_close($ch);
		
		$accountNumber = $this->getAccountNumber($to);
		
		if($output){
			$outputDecoded = json_decode($output);
			$accessToken = $outputDecoded->access_token;
		} else {
			return $output;
		}
		
		/* transfer part */
		
		$url = 'https://api-uat.unionbankph.com/partners/sb/partners/v1/transfers/single';
		if(true || is_null($details))
		{
			$postData2 = new stdClass();
			$postData2->senderTransferId = "000010";
			$postData2->transferRequestDate = "2017-10-10T12:11:50Z";
			$postData2->accountNo = $accountNumber;
				$amountObj = new stdClass();
				$amountObj->currency = "PHP";
				$amountObj->value = $amount;
			$postData2->amount = $amountObj;
			$postData2->remarks = "Transfer remarks";
			$postData2->particulars = "Transfer particulars";
				$info1 = new stdClass();
				$info1->index = 1;
				$info1->name = "Recipient";
				$info1->value = "Juan Dela Cruz";
				$info2 = new stdClass();
				$info2->index = 2;
				$info2->name = "Message";
				$info2->value = "Happy Birthday";
			$postData2->info = array($info1,$info2);
			$postData = json_encode($postData2);
		}

        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL,$url);
		curl_setopt($ch2, CURLOPT_POST, true);
		curl_setopt($ch2, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, 
			array(
				"accept: application/json",
				"content-type: application/json",
				"Authorization: Bearer $accessToken",
				"x-ibm-client-id: ".$this->getClientId(),
				"x-ibm-client-secret: ". $this->getClientSecret(),
				"x-partner-id: ".$from[2],
			)
		); 

        $output=curl_exec($ch2);

        curl_close($ch2);
		
		return $output;
	}
	
	public function connect($username,$password){
		/*
			curl https://api-uat.unionbankph.com/partners/sb/convergent/v1/oauth2/token \
			  -H 'accept: application/json' \
			  -H 'content-type: application/x-www-form-urlencoded' \
			  -X POST \
			  -d 'grant_type=password&client_id=<YOUR CLIENT ID>&client_secret=<YOUR CLIENT SECRET>&redirect_uri=<REPLACE THIS VALUE>&username=<REPLACE THIS VALUE>&password=<REPLACE THIS VALUE>&refresh_token=<REPLACE THIS VALUE>'
		*/
		$url = 'https://api-uat.unionbankph.com/partners/sb/partners/v1/oauth2/token';
        $postData = "grant_type=password&client_id=".$this->getClientId()."&username=maangela&password=pass123&scope=Transfers";
		/*array(
			'grant_type' => 'password',
			'client_id' => $this->getClientId(),
			'username' => 'madalawis',
			'password' => 'pass123',
			'scope' => 'Transfers',
		);*/

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				"content-type: application/x-www-form-urlencoded",
			)
		); 

        $output=curl_exec($ch);

        curl_close($ch);

        return $output;
	}
}