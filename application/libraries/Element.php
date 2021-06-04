<?php

namespace Custom\Libraries;

use CI_Controller;
use Exception;
use stdClass;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CodeIgniter Rest Controller
 * A fully RESTful server implementation for CodeIgniter using one library, one config file and one controller.
 *
 * @package         CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 * @version         3.0.0
 */
abstract class Element extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->css = null;
        $this->js = null;
        $this->theme = null;
        $this->title_submenu = null;
        $this->content = null;
    }
    public function theme($url)
    {
        $this->theme = $url;
    }
    public function title_submenu($url)
    {
        $this->title_submenu = $url;
    }
    public function content($url, $content = [])
    {
        $this->content = $this->content . "\n" . $this->load->view($url, $content, true);
    }


    public function css($url)
    {
        $this->css = $this->css . "\n" . $this->load->view($url, [], true);
    }
    public function js($url)
    {
        $this->js = $this->js . "\n" . $this->load->view($url, [], true);
    }
    public function render($data = [])
    {
        $data = (object)$data;
        if (property_exists($data, 'css')) {
            $css = array_map(function ($v) {
                return "<link href='$v' rel='stylesheet'>";
            }, $data->css);
            $css = implode("\n", $css);
        } else {
            $css = "";
        }
        if (property_exists($data, 'js')) {
            $js = array_map(function ($v) {
                return "<script src='$v'></script>";
            }, $data->js);
            $js = implode("\n", $js);
        } else {
            $js = "";
        }
        echo ($this->load->view(
            "layouts/admin_gentelella",
            [
                "title_submenu" => $this->title_submenu,
                "css"           =>  $this->css . $css,
                "content"       =>  $this->content,
                "js"            =>  $this->js . $js
            ],
            TRUE
        ));
    }
}
