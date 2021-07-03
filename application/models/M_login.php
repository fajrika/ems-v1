<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class m_login extends CI_Model {
    function __construct() {
		parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }
    public function status_login(){
        if($this->session->userdata('username') and $this->session->userdata('password')){
            $username = $this->session->userdata('username');
            $password = $this->session->userdata('password');
            $query = $this->db->query("SELECT * FROM [user] WHERE username = '$username' AND password = '$password'");
            $row = $query->row(); 
            if($row){
                return true;
            }else{
                $this->unset_session();
            }
        }
        return false;
    }
    public function cek_user($username,$password)
    {
        $password = md5($password);
        // $query = $this->db->query("SELECT * FROM [user] WHERE username = '$username' AND password = '$password'");
        return $query = $this->db
            ->where('username', stripslashes(strip_tags(htmlspecialchars($username, ENT_QUOTES))))
            ->where('password', $this->db->escape_like_str($password))
            ->get('user')
            ->row();
    }
    public function set_session($name,$username,$password,$group_user_id,$unit_id){
        $this->session->set_userdata([
            'name'      => $name,
            'username'  => $username,
            'password'  => $password,
            'group'     => $group_user_id,
            'unit_id'     => $unit_id
            
        ]);
        if($this->session->userdata('username')){
            return true;
        }
    }public function unset_session(){
        $this->session->unset_userdata(['name','username','password','group']);
    }

}