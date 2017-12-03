<?php
defined('BASEPATH') OR exit('No direct script access allowed');


if (!function_exists('uni_get_task'))
{
    function uni_get_task($transaction_id, $task_id = null)
    {
        $ci = &get_instance();
        $ci->load->database();
        
        if($transaction_id){
            
            $sql = 'SELECT * FROM tasks WHERE transaction_id ='.$ci->db->escape($transaction_id);
            
            if($task_id){
                $sql .= ' AND id = '.$ci->db->escape($task_id); 
            }
                
            $query = $ci->db->query($sql);
            
            $result = $query->result_array();
            if(count($result)){

                $tasks = null;
                foreach($result as $row){
                    $row['id'] = intval($row['id']);
                    $row['transaction_id'] = intval($row['transaction_id']);
                    $tasks[] = $row;
                    if($task_id){
                        return $row; 
                    }                
                }

                return $tasks; 
            }
            
            return array();
        }
    }
}

if (!function_exists('uni_get_config'))
{
    function uni_get_config($key){
       $ci = &get_instance();
       $ci->load->database(); 
       
       if($key){
           $sql = 'SELECT * FROM config WHERE config_key = '.$key;
           $query = $ci->db->query($sql);
           
           $result = $query->result_array();
           if(count($result)){
               foreach($result as $row){
                   return $row['config_value'];
               }
           }
       }
    }
}

if (!function_exists('uni_get_userdetails'))
{
    function uni_get_userdetails($username,$key){
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
}

if (!function_exists('uni_read_post_json')){
    
    function uni_read_post_json(){
        $entityBody = file_get_contents('php://input');
        $json = json_decode($entityBody,true);
        return $json;
    }
    
}

