<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Twilio extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->helper(['jwt', 'authorization']); 

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->load->database();
        $this->db->database = "ems";
    }
    public function index_post($tipe=null)
    {
        $this->load->helper('file');
        
        write_file("./log/" . date("y-m-d") . '_twilio.txt', "\n" . date("y-m-d h:i:s") . " = POST !", 'a+');

        write_file("./log/" . date("y-m-d") . '_twilio.txt', "\n" . json_encode($this->post()) , 'a+');

        $parameter = (object)json_decode($this->input->raw_input_stream, true);
        $head = (object)$this->head();
        write_file("./log/" . date("y-m-d") . '_twilio.txt', "\n" . json_encode($head) , 'a+');

        $this->set_response([
            "success"
        ], REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }
}
