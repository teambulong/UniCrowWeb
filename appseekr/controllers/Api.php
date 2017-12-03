<?php

require(APPPATH.'/libraries/REST_Controller.php');
 
class Api extends REST_Controller{
    
    public function __construct()
    {
        parent::__construct();

        $this->load->model('seekr_model');
    }

    function test_get(){
        $result = $this->seekr_model->getall();
		//{"status":"success/error",data:object/array}
		$responseCode = 200;
        if(!$result){
			$responseCode = 404;
        }
		$this->response($this->getJsonResult($result), $responseCode);
    }
	
	public function getJsonResult($result){
		$jsonResult = array();

        if($result){
			$jsonResult['status'] = 'success';
			$jsonResult['totalCount'] = 1;
			$jsonResult['data'] = $result;
        } else {
			$jsonResult['status'] = 'error';
			$jsonResult['totalCount'] = 0;
			$jsonResult['data'] = array();
        }
		return json_encode($jsonResult);
	}
	
	function atm_get(){
        $result = $this->seekr_model->getatm();
		//{"status":"success/error",data:object/array}
		$responseCode = 200;
        if(!$result){
			$responseCode = 404;
        }
		$this->response($this->getJsonResult($result), $responseCode);
	}

	function sendpaymenttest_get(){
		$fromAccount = array('maangela','pass123',30354);
		$toAccount = 'unicrow';
        $result = $this->seekr_model->sendpayment($fromAccount,$toAccount,100); /* from,to,amount*/
		//{"status":"success/error",data:object/array}
		$responseCode = 200;
        if(!$result){
			$responseCode = 404;
        }

		$fromAccount = array('unicrow','pass123',42714);
		$toAccount = 'maangela1';
        $result2 = $this->seekr_model->sendpayment($fromAccount,$toAccount,100); /* from,to,amount*/
		//{"status":"success/error",data:object/array}
		$responseCode = 200;
        if(!$result){
			$responseCode = 404;
        }
		$this->response($this->getJsonResult($result).'<br /><br />'.$this->getJsonResult($result2), $responseCode);
	}
	
	function connect_get(){
        $result = $this->seekr_model->connect('madalawis','pass123');
		//{"status":"success/error",data:object/array}
		$responseCode = 200;
        if(!$result){
			$responseCode = 404;
        }
		$this->response($this->getJsonResult($result), $responseCode);
	}
	
	function connector_get(){
		var_dump($_REQUEST);
	}
}