<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('fix_whitespace')) {
	function fix_whitespace($string)
	{
		return trim(preg_replace('!\s+!', ' ', $string));
	}
}

if (!function_exists('range_percent')) {
	function range_percent($number)
	{
		$number = trim($number);

		if ($number>100)
		{
			$number = 100;
		}
		else if ($number<0)
		{
			$number = 0;
		}
		return $number;
	}
}
if (!function_exists('only_number')) {
	function only_number($string)
	{
		return preg_replace('/\D/', '', $string);
	}
}
if (!function_exists('only_alfabet')) {
	function only_alfabet($string)
	{
		return trim(preg_replace('/[^a-zA-Z\']/', '', $string));
	}
}

if (!function_exists('unlink_file')) {
    function unlink_file($link_file)
    {
    	$namafile = explode('/', $link_file);
    	if ($namafile[count($namafile)-1] != "")
    	{
	    	if (file_exists($link_file))
	    	{
	    		unlink($link_file);
	    	}
    	}
    }
}

if ( ! function_exists('load_css'))
{
	function load_css($css=array())
	{
		$data = array(
			 'select2'			=> '
			 						<link href="'.base_url().'vendors/select2/dist/css/select2.min.css" rel="stylesheet">'
			 ,'switchery'		=> '
			 						<link href="'.base_url().'vendors/switchery/dist/switchery.min.css" rel="stylesheet">'
			 ,'datetimepicker'	=> '
			 						<link href="'.base_url().'vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">'
			 ,'pnotify'			=> '
				 					<link href="'.base_url().'vendors/pnotify/dist/pnotify.css" rel="stylesheet">
				 					<link href="'.base_url().'vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
				 					<link href="'.base_url().'vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">'
		);

		$res = "";
		foreach ($css as $r => $value)
		{
			if (array_key_exists($value, $data))
			{
				$res = $res . $data[$value];
			}
		}
		return $res;
	}
}

if ( ! function_exists('load_js'))
{
	function load_js($js=array())
	{
		$data = array(
			 'select2'		=> '
			 					<script src="'.base_url().'vendors/select2/dist/js/select2.min.js"></script>'
			,'switchery'		=> '
			 					<script src="'.base_url().'vendors/switchery/dist/switchery.min.js"></script>'
			,'datetimepicker'		=> '
			 					<script src="'.base_url().'vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>'
			,'inputmask'	=> '
			 					<script src="'.base_url().'vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>'
			,'moment'	=> '
			 					<script src="'.base_url().'vendors/moment/min/moment.min.js"></script>'
		);

		$res = "";
		foreach ($js as $r => $value)
		{
			if (array_key_exists($value, $data))
			{
				$res = $res . $data[$value];
			}
		}
		return $res;
	}
}
if ( ! function_exists('nominal'))
{
	function nominal($angka,$mata_uang="RP. ",$no_koma=2, $pemisah=".")
	{
		if ($pemisah==".")
		{
			$hasil_rupiah = $mata_uang . number_format((empty($angka) ? 0 : $angka),$no_koma,',','.');
		}
		else
		{
			$hasil_rupiah = $mata_uang . number_format((empty($angka) ? 0 : $angka),$no_koma,'.',',');
		}
		return $hasil_rupiah;
	}
}
if ( ! function_exists('remove_double_slice'))
{
	function remove_double_slice($string="")
	{
		if (strpos($string, "http://") !== false)
		{
			$string = substr($string, 0, 7) . preg_replace('#/+#','/',substr($string, 7, strlen($string)-1)).'/';
		}
		else if (strpos($string, "https://") !== false)
		{
			$string = substr($string, 0, 8) . preg_replace('#/+#','/',substr($string, 8, strlen($string)-1)).'/';
		}

		return $string;
	}
}

if ( ! function_exists('cek_null_empty'))
{
	function cek_null_empty($data="")
	{
		if ($data==""||$data==null)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}
