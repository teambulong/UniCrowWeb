<?php
class Rec extends CI_Controller {
    
   
    public function lister(){
        $this->load->database();
        $email = $this->db->escape($_REQUEST['email']);
        $sql = 'SELECT * FROM transactions WHERE rec_email='.$email;
        $query = $this->db->query($sql);
        $result = $query->result_array();
        
        if(count($result)){
            ?>
            <ul style="width:100%; list-style-type: none; margin:0.5 0; padding-left:0;">
            <?php
            foreach($result as $row){
            ?>
                <li style="padding:1em; margin-bottom:1em; box-shadow: 3px 3px 5px 2px #ccc; font-family:sans-serif;">
                    <p style="color:orange;">
                    <?php echo $row['title']; ?>
                    </p> <!-- D, j M Y -->
                    <p style="color:#bbb;">
                        Date created: <b>
                            <?php
                                $t = strtotime($row['created_at']);
                                echo date('D, j M Y',$t);
                            
                                ?>
                        </b>
                    </p>
                </li>
            <?php
            }
            ?>
            </ul>
            <?php
        }
    }
    
    
}