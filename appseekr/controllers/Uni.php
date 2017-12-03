<?php
class Uni extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        $this->load->model('seekr_model');
    }
    
    public function test(){
        echo 'hello world';
    }
    
    public function escrow(){
        
        $this->load->helper('uni_helper');
        
        $this->load->database();
        
        if(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'sendtoescrow'){
            
            $sql = 'SELECT * FROM users WHERE id='.$this->db->escape($_REQUEST['user_id']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $user = null;
                foreach($result as $row){
                    $user = $row;
                    break;
                }
                
                $sql = 'SELECT * FROM transactions WHERE id='.$this->db->escape($_REQUEST['transaction_id']);
                $query = $this->db->query($sql);
                $result = $query->result_array();
                if(count($result)){

                    $transaction = null;
                    foreach($result as $row){
                        $transaction = $row;
                        break;
                    }
                    
                    $fromAccount = array($user['api_username'],$user['api_password'],$user['account_code']);
                    
                    $toAccount = 'unicrow';
                    
                    $this->seekr_model->sendpayment($fromAccount,$toAccount,intval($transaction['amount']));
                    
                    $result = array('status'=>'success',
                                    'data'=>'ok'
                    );
                
                    echo json_encode($result);
                    
                }
                else{
                    $result = array('status'=>'failed',
                                'data'=>'payment failed'
                    );
                
                    echo json_encode($result);
                }
            }
            
            else{
                $result = array('status'=>'failed',
                                'data'=>'payment failed'
                );
                echo json_encode($result);  
            }  
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'sendfromescrow'){
           
            $sql = 'SELECT * FROM transactions WHERE id='.$this->db->escape($_REQUEST['transaction_id']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            
            if(count($result)){

                $transaction = null;
                foreach($result as $row){
                    $transaction = $row;
                    break;
                }
                
                $sql = 'SELECT * FROM users WHERE email='.$transaction['rec_email'];
                $query = $this->db->query($sql);
                $result = $query->result_array();
                
                if(count($result)){

                    $user = null;
                    foreach($result as $row){
                        $user = $row;
                        break;
                    }
                    
                    $fromAccount = array('unicrow','pass123','42714');
                    
                    $toAccount = $user['api_username'];
                    
                    $this->seekr_model->sendpayment($fromAccount,$toAccount,intval($transaction['amount']));
                    
                    $result = array('status'=>'success',
                                    'data'=>'ok'
                    );
                
                    echo json_encode($result);
                    
                }
                else{
                    $result = array('status'=>'failed',
                                    'data'=>'payment failed'
                    );
                
                    echo json_encode($result);
                }
            }
            
            else{
                $result = array('status'=>'failed',
                                'data'=>'payment failed'
                );
                echo json_encode($result);  
            }  
            
        }
        
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'refundfromescrow'){
        
            $sql = 'SELECT * FROM users WHERE id='.$this->db->escape($_REQUEST['user_id']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $user = null;
                foreach($result as $row){
                    $user = $row;
                    break;
                }
                
                $sql = 'SELECT * FROM transactions WHERE id='.$this->db->escape($_REQUEST['transaction_id']);
                $query = $this->db->query($sql);
                $result = $query->result_array();
                if(count($result)){

                    $transaction = null;
                    foreach($result as $row){
                        $transaction = $row;
                        break;
                    }
                    
                    $fromAccount = array('unicrow','pass123','42714');
                    
                    $toAccount = $user['api_username'];
                    
                    $this->seekr_model->sendpayment($fromAccount,$toAccount,intval($transaction['amount']));
                    
                    $result = array('status'=>'success',
                                    'data'=>'ok'
                    );
                
                    echo json_encode($result);
                    
                }
                else{
                    $result = array('status'=>'failed',
                                'data'=>'payment failed'
                    );
                
                    echo json_encode($result);
                }
            }
            
            else{
                $result = array('status'=>'failed',
                                'data'=>'payment failed'
                );
                echo json_encode($result);  
            } 
            
            
        }
        
    }
    
    public function notifs(){
        $this->load->database();
        
        if(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'list'){
            $result = array('status'=>'failed',
                            'data'=>'User not found'
            );
            
            $sql = 'SELECT * FROM notifications WHERE toreaduserid='.$this->db->escape($_REQUEST['toreaduserid']);
            $query = $this->db->query($sql);
            $res = $query->result_array();
            if(count($res)){
                $notifs = array();
                foreach($res as $row){
                    $notifs[] = $row;
                }   
            }
            
            $result = array(
                'status'=>'success',
                'data'=> $notifs,
                'totalCount'=>count($notifs)
            );

            echo json_encode($result);
        }
        elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
            
            try{
                $data = array();
                if(isset($_REQUEST['toreaduserid'])) $data['toreaduserid'] = $_REQUEST['toreaduserid'];
                if(isset($_REQUEST['msg'])) $data['msg'] = $_REQUEST['msg'];
                if(isset($_REQUEST['is_read'])) $data['is_read'] = $_REQUEST['is_read'];

                $this->db->where('id', $_REQUEST['id']);
                $this->db->update('notifs', $data);   

                $result = array('status'=>'success',
                                'data'=>$data
                );

                echo json_encode($result);
            }
            catch(Exception $e){
                $result = array('status'=>'failed',
                            'data'=>'Notification not found'
                );
                
                echo json_encode($result);
            }
            
        }
    }
    
    public function user(){
        
        $this->load->database();
        
        if(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'login'){
            
            $result = array('status'=>'failed',
                        'data'=>'Auth failure'
            );
            
            $sql = 'SELECT * FROM users WHERE email='.$this->db->escape($_REQUEST['email']) .' AND password='.$this->db->escape($_REQUEST['password']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $user = null;
                foreach($result as $row){
                    $user = $row;
                    break;
                }

                $user['id'] = intval($user['id']);

                $result = array(
                    'status'=>'success',
                    'data'=> $user,
                ); 
            }

            echo json_encode($result);
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'search'){
            
            $result = array('status'=>'failed',
                            'data'=>'User not found'
            );
            
            $sql = 'SELECT * FROM users WHERE id='.$this->db->escape($_REQUEST['id']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $user = null;
                foreach($result as $row){
                    $user = $row;
                    break;
                }

                $user['id'] = intval($user['id']);

                $result = array(
                    'status'=>'success',
                    'data'=> $user,
                );
                
                echo json_encode($result);
            }
            
            echo json_encode($result);
            
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'create'){
            try{
                $data = array(
                        'firstname' => $_REQUEST['firstname'],
                        'lastname' => $_REQUEST['lastname'],
                        'email' => $_REQUEST['email'],
                        'password' => $_REQUEST['password'],
                );

                $this->db->insert('users', $data);
                
                $result = array('status'=>'success',
                                'data'=>$data
                );
                
                echo json_encode($result);
            }
            catch(Exception $e){
                
                $result = array('status'=>'failed',
                            'data'=>'Saving failed'
                );
                
                echo $result;
            }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'edit'){
            
            try{
                
                $data = array();
                if(isset($_REQUEST['firstname'])) $data['firstname'] = $_REQUEST['firstname'];
                if(isset($_REQUEST['lastname'])) $data['lastname'] = $_REQUEST['lastname'];
                if(isset($_REQUEST['email'])) $data['email'] = $_REQUEST['email'];
                if(isset($_REQUEST['password'])) $data['password'] = $_REQUEST['password'];

                $this->db->where('id', $_REQUEST['id']);
                $this->db->update('users', $data);   
                
                $result = array('status'=>'success',
                                'data'=>$data
                );
                
                echo json_encode($result);
                
                
            }catch(Exception $e){
                
                $result = array('status'=>'failed',
                                'data'=>'Saving Failed'
                );
                
            }            
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'searchbyname'){
            
            $result = array('status'=>'failed',
                        'data'=>'No users found'
            );
            
            $this->db->or_like('firstname', $_REQUEST['name']);
            $this->db->or_like('lastname', $_REQUEST['name']);
            $rows = $this->db->get('users')->result_array();
            
            if(count($rows)){
                
                $users = array();
                foreach($rows as $row){
                    $row['id'] = intval($row['id']);
                    $users[] = $row;
                    break;
                }

                $result = array(
                    'status'=>'success',
                    'data'=> $users,
                ); 
            }

            echo json_encode($result);
                        
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'searchbyemail'){
            
            $result = array('status'=>'failed',
                        'data'=>'No users found'
            );
            
            $this->db->like('email', $_REQUEST['email']);
            $rows = $this->db->get('users')->result_array();
            
            if(count($rows)){
                
                $users = array();
                foreach($rows as $row){
                    $row['id'] = intval($row['id']);
                    $users[] = $row;
                    break;
                }

                $result = array(
                    'status'=>'success',
                    'data'=> $users,
                ); 
            }

            echo json_encode($result);
                        
        }

    }
    
    public function transaction(){
        $this->load->helper('uni_helper');
        $this->load->database();
        
        if(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'list'){
            
            $result = array('status'=>'failed',
                            'data'=>'User not found'
            );
            
            $sql = 'SELECT * FROM transactions WHERE user_id='.$this->db->escape($_REQUEST['user_id']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $transactions = array();
                foreach($result as $row){
                    $transaction = $row;
                    $transaction['id'] = intval($row['id']);
                    $transaction['user_id'] = intval($row['user_id']);
                    $transaction['created_at'] = strtotime($row['created_at']) * 1000; 
                    $transaction['updated_at'] = strtotime($row['updated_at']) * 1000; 
                    $transaction['tasks'] = uni_get_task($transaction['id']);
                    $transactions[] = $transaction;
                }

                $result = array(
                    'status'=>'success',
                    'data'=> $transactions,
                    'totalCount'=>count($transaction)
                );
                
                echo json_encode($result);
            }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'listbyuserid'){
            $result = array('status'=>'failed',
                            'data'=>'User not found'
            );
            
            $sql = 'SELECT * FROM transactions WHERE user_id='.$this->db->escape($_REQUEST['user_id']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $transactions = array();
                foreach($result as $row){
                    $transaction = $row;
                    $transaction['id'] = intval($row['id']);
                    $transaction['user_id'] = intval($row['user_id']);
                    $transaction['created_at'] = strtotime($row['created_at']) * 1000; 
                    $transaction['updated_at'] = strtotime($row['updated_at']) * 1000; 
                    $transactions[] = $transaction;
                }

                $result = array(
                    'status'=>'success',
                    'data'=> $transactions,
                    'totalCount'=>count($transaction)
                );
                
                echo json_encode($result);
            }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'listbyrecemail'){
            $result = array('status'=>'failed',
                            'data'=>'User not found'
            );
            
            $sql = 'SELECT * FROM transactions WHERE rec_email='.$this->db->escape($_REQUEST['rec_email']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $transactions = array();
                foreach($result as $row){
                    $transaction = $row;
                    $transaction['id'] = intval($row['id']);
                    $transaction['user_id'] = intval($row['user_id']);
                    $transaction['created_at'] = strtotime($row['created_at']) * 1000; 
                    $transaction['updated_at'] = strtotime($row['updated_at']) * 1000; 
                    $transactions[] = $transaction;
                }

                $result = array(
                    'status'=>'success',
                    'data'=> $transactions,
                    'totalCount'=>count($transaction)
                );
                
                echo json_encode($result);
            }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'get'){
            
            $result = array('status'=>'failed',
                            'data'=>'Transaction not found'
            );
            
            $sql = 'SELECT * FROM transactions WHERE id='.$this->db->escape($_REQUEST['id']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(count($result)){

                $transaction = null;
                foreach($result as $row){
                    $transaction = $row;
                    $transaction['id'] = intval($row['id']);
                    $transaction['user_id'] = intval($row['user_id']);
                    $transaction['created_at'] = strtotime($row['created_at']) * 1000;
                    $transaction['updated_at'] = strtotime($row['updated_at']) * 1000;
                    $transaction['tasks'] = uni_get_task($transaction['id']);
                    break;
                }

                $result = array(
                    'status'=>'success',
                    'data'=> $transaction,
                );
                
                echo json_encode($result);
            }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'create'){
            
            try{
                $data = array(
                        'user_id' => $_REQUEST['user_id'],
                        'title' => $_REQUEST['title'],
                        'details' => $_REQUEST['details'],
                        'amount' => $_REQUEST['amount'],
                        'acct_no' => $_REQUEST['acct_no'],
                        'rec_firstname' => $_REQUEST['rec_firstname'],
                        'rec_lastname' => $_REQUEST['rec_lastname'],
                        'rec_email' => $_REQUEST['rec_email'],
                        'status'=> isset($_REQUEST['status']) ? $_REQUEST['status'] : 0,
                );

                $this->db->insert('transactions', $data);
                
                $result = array('status'=>'success',
                                'data'=>$data
                );
                
                echo json_encode($result);
            }
            catch(Exception $e){
                
                $result = array('status'=>'failed',
                            'data'=>'Saving failed'
                );
                
                echo json_encode($result);
                
            }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'createwhole'){
             try{
                 
                $data = array(
                    'user_id' => $_REQUEST['user_id'],
                    'title' => $_REQUEST['title'],
                    'details' => $_REQUEST['details'],
                    'amount' => $_REQUEST['amount'],
                    'acct_no' => $_REQUEST['acct_no'],
                    'rec_firstname' => $_REQUEST['rec_firstname'],
                    'rec_lastname' => $_REQUEST['rec_lastname'],
                    'rec_email' => $_REQUEST['rec_email'],
                    'status'=> isset($_REQUEST['status']) ? $_REQUEST['status'] : 0,
                );

                $this->db->insert('transactions', $data);
                $insert_id = $this->db->insert_id();

                if(isset($_REQUEST['tasks'])){
                    
                    $tasks = json_decode($_REQUEST['tasks'],true);
                    
                    foreach($tasks as $task){
                        $data = array(
                            'transaction_id' => $insert_id,
                            'details' => $task['details'],
                            'is_done'=> isset($task['is_done']) ? $task['is_done'] : 0,
                        );

                        $this->db->insert('task', $data);
                    }
                }
                
                $result = array('status'=>'success',
                                'data'=>$insert_id,
                );
                
                echo json_encode($result);
                 
             }catch(Exception $e){
                 $result = array('status'=>'failed',
                                'data'=>'error',
                );
                
                echo json_encode($result);
             }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'jcreatewhole'){
             
            $datain = uni_read_post_json();
            
            
            try{
                $data = array();
                if(isset($_REQUEST['user_id'])) $data['user_id'] = $_REQUEST['user_id'];
                if(isset($datain['title'])) $data['title'] = $datain['title'];
                if(isset($datain['details'])) $data['details'] = $datain['details'];
                if(isset($datain['amount'])) $data['amount'] = $datain['amount'];
                if(isset($datain['acct_no'])) $data['acct_no'] = $datain['acct_no'];
                if(isset($datain['rec_firstname'])) $data['rec_firstname'] = $datain['rec_firstname'];
                if(isset($datain['rec_lastname'])) $data['rec_lastname'] = $datain['rec_lastname'];
                if(isset($datain['rec_email'])) $data['rec_email'] = $datain['rec_email'];

                $this->db->insert('transactions', $data);
                
                
                $insert_id = $this->db->insert_id();

                if(isset($datain['tasks']) && !empty($datain['tasks'])){
                   
                    $tasks = $datain['tasks'];
        
                    foreach($tasks as $task){
                        $data = array(
                            'transaction_id' => $insert_id,
                            'details' => $task['details'],
                            'is_done'=> isset($task['is_done']) ? $task['is_done'] : 0,
                        );

                        $this->db->insert('tasks', $data);
                    }

                }
              
            
                $result = array('status'=>'success',
                                'data'=>(object)$data,
                );
                
                echo json_encode($result);
            
                 
             }catch(Exception $e){
                 $result = array('status'=>'failed',
                                'data'=>'error',
                );
                
                echo json_encode($result);
             }
             
        
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'edit'){
            
            try{
                
                $data = array();
                if(isset($_REQUEST['user_id'])) $data['user_id'] = $_REQUEST['user_id'];
                if(isset($_REQUEST['title'])) $data['title'] = $_REQUEST['title'];
                if(isset($_REQUEST['details'])) $data['details'] = $_REQUEST['details'];
                if(isset($_REQUEST['amount'])) $data['amount'] = $_REQUEST['amount'];
                if(isset($_REQUEST['acct_no'])) $data['acct_no'] = $_REQUEST['acct_no'];
                if(isset($_REQUEST['rec_firstname'])) $data['rec_firstname'] = $_REQUEST['rec_firstname'];
                if(isset($_REQUEST['rec_lastname'])) $data['rec_lastname'] = $_REQUEST['rec_lastname'];
                if(isset($_REQUEST['rec_email'])) $data['rec_email'] = $_REQUEST['rec_email'];
                if(isset($_REQUEST['status'])) $data['status'] = $_REQUEST['status'];

                $this->db->where('id', $_REQUEST['id']);
                $this->db->update('transactions', $data);   
                
                $result = array('status'=>'success',
                                'data'=>$data
                );
                
                echo json_encode($result);
                
                
            }catch(Exception $e){
                
                $result = array('status'=>'failed',
                                'data'=>'Saving Failed'
                );
                
            }            
        }
    }
    
    public function task(){
        $this->load->helper('uni_helper');
        $this->load->database();
        
        if(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'list'){
            
            $result = array('status'=>'failed',
                        'data'=>'no task data',
            );
            
            $result = uni_get_task($_REQUEST['transaction_id']);
            
            if(count($result)){
                $result = array(
                    'status'=>'success',
                    'data'=> $result,
                ); 
            }

            echo json_encode($result);
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'get'){
            $result = array('status'=>'failed',
                        'data'=>'no task data',
            );
            
            $result = uni_get_task($_REQUEST['transaction_id'], $_REQUEST['id']);
            
            if(count($result)){
                
                $result = array(
                    'status'=>'success',
                    'data'=> $result,
                ); 
            }
            
            echo json_encode($result);
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'create'){
            try{
                $data = array(
                        'transaction_id' => $_REQUEST['transaction_id'],
                        'details' => $_REQUEST['details'],
                        'is_done'=> isset($_REQUEST['is_done']) ? $_REQUEST['is_done'] : 0,
                );

                $this->db->insert('task', $data);
                
                $result = array('status'=>'success',
                                'data'=>$data
                );
                
                echo json_encode($result);
            }
            catch(Exception $e){
                
                $result = array('status'=>'failed',
                            'data'=>'Saving failed'
                );
                
                echo json_encode($result);   
            }
        }
        elseif(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'edit'){
            
            try{
                
                $data = array();
                if(isset($_REQUEST['transaction_id'])) $data['transaction_id'] = $_REQUEST['transaction_id'];
                if(isset($_REQUEST['details'])) $data['details'] = $_REQUEST['details'];
                if(isset($_REQUEST['is_done'])) $data['is_done'] = $_REQUEST['is_done'];

                $this->db->where('id', $_REQUEST['id']);
                $this->db->update('tasks', $data);   
                
                $result = array('status'=>'success',
                                'data'=>$data
                );
                
                echo json_encode($result);
                
                
            }catch(Exception $e){
                
                $result = array('status'=>'failed',
                                'data'=>'Saving Failed'
                );
                
            }            
        }
        
    }
            
}
