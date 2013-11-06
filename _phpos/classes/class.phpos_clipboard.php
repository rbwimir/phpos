<?php
/*
**********************************

	PHPOS Web Operating system
	MIT License
	(c) 2013 Marcin Szczyglinski
	szczyglis83@gmail.com
	GitHUB: https://github.com/phpos/
	File version: 1.2.8, 2013.10.26
 
**********************************
*/
if(!defined('PHPOS'))	die();	

	 
/*
**************************
*/
 	
class phpos_clipboard
{
	private 
		$file_id,	
		$file_name,
		$multiple,
		$file_connect_id,	
		$mode,
		$server,
		$file_fs;		
		 
/*
**************************
*/
 	
	public function __construct() {
		
		if(!is_array($_SESSION['phpos_clipboard']))
		{
			$_SESSION['phpos_clipboard'] = array();
			$_SESSION['phpos_clipboard']['id'] = null;	
			$_SESSION['phpos_clipboard']['file_name'] = null;			
			$_SESSION['phpos_clipboard']['server'] = false;
			$_SESSION['phpos_clipboard']['multiple'] = false;
			$_SESSION['phpos_clipboard']['fs'] = null;
			$_SESSION['phpos_clipboard']['mode'] = 'copy';
			$_SESSION['phpos_clipboard']['connect_id'] = null;
		}		
	}
	
	 
/*
**************************
*/
 	public function set_mode($mode)
	{
		$_SESSION['phpos_clipboard']['mode'] = $mode;
		console::log(array('clipboard.mode' => $mode));
	}
	 
/*
**************************
*/
 		
	public function set_name($name)
	{
		if(!$_SESSION['phpos_clipboard']['multiple'])
		{
			$_SESSION['phpos_clipboard']['file_name'] = $name;
			console::log(array('clipboard.name' => $name));			
			
		} else {
			
			if(!is_array($_SESSION['phpos_clipboard']['file_name'])) $_SESSION['phpos_clipboard']['file_name'] = array();
			$_SESSION['phpos_clipboard']['file_name'][] = $name;
			
			console::log(array('clipboard.name' => '[array] '.$name));			
		}
	}
		 
/*
**************************
*/
 	
	public function set_server($val)
	{
		$_SESSION['phpos_clipboard']['server'] = $val;		
		console::log(array('clipboard.server' => $val));	
	}
		 
/*
**************************
*/
 	
	public function set_multiple($val)
	{
		$_SESSION['phpos_clipboard']['multiple'] = $val;
		console::log(array('clipboard.multiple' => $val));	
	}
		 
/*
**************************
*/
 	
	public function set_source_win($id)
	{
		$_SESSION['phpos_clipboard']['source_win'] = $id;
		console::log(array('clipboard.source_win' => $id));			
	}
		 
/*
**************************
*/
 	
	public function get_source_win()
	{
		return $_SESSION['phpos_clipboard']['source_win'];
	}
		 
/*
**************************
*/
 	
	public function get_name()
	{
		return $_SESSION['phpos_clipboard']['file_name'];
	}
		 
/*
**************************
*/
 	
	public function add_clipboard($id, $fs, $connect_id = null)
	{		
		if(!empty($id) && !empty($fs))
		{
			$_SESSION['phpos_clipboard']['fs'] = $fs;
			$_SESSION['phpos_clipboard']['connect_id'] = $connect_id;
			console::log(array('@clipboard' => 'add', 'id' => $id, 'fs' => $fs));			
			
			if(!$_SESSION['phpos_clipboard']['multiple'])
			{
				$_SESSION['phpos_clipboard']['id'] = $id;
				
			} else {
				
				if(!is_array($_SESSION['phpos_clipboard']['id'])) $_SESSION['phpos_clipboard']['id'] = array();
				$_SESSION['phpos_clipboard']['id'][] = $id;
			}
			
			return true;
		}		
	}
	 
/*
**************************
*/
 	
	public function get_clipboard()
	{
		$this->file_id = $_SESSION['phpos_clipboard']['id'];
		$this->file_name = $_SESSION['phpos_clipboard']['file_name'];
		$this->multiple = $_SESSION['phpos_clipboard']['multiple'];
		$this->file_fs = $_SESSION['phpos_clipboard']['fs'];
		$this->file_connect_id = $_SESSION['phpos_clipboard']['connect_id'];		
	}
		 
/*
**************************
*/
 	
	public function get_mode()
	{
		return $_SESSION['phpos_clipboard']['mode'];
	}
		 
/*
**************************
*/
 	
	public function is_server()
	{
		if($_SESSION['phpos_clipboard']['server']) return true;
	}
		 
/*
**************************
*/
 	
	public function is_multiple()
	{
		if($_SESSION['phpos_clipboard']['multiple']) return true;
	}
/*
**************************
*/
 	
	public function get_file_id()
	{
		return $this->file_id;
	}
	 
/*
**************************
*/
 	
	public function get_file_connect_id()
	{
		return $this->file_connect_id;
	}
	
 
/*
**************************
*/
 	
	
	public function get_file_fs()
	{
		return $this->file_fs;
	}
	
	 
/*
**************************
*/
 	
	public function reset_clipboard()
	{
		$_SESSION['phpos_clipboard'] = array();
		$_SESSION['phpos_clipboard']['id'] = null;		
		$_SESSION['phpos_clipboard']['file_name'] = null;
		$_SESSION['phpos_clipboard']['server'] = false;	
		$_SESSION['phpos_clipboard']['multiple'] = false;	
		$_SESSION['phpos_clipboard']['fs'] = null;
		$_SESSION['phpos_clipboard']['connect_id'] = null;
		
		console::log(array('@clipboard' => 'reset'));		
	}
	 
/*
**************************
*/
 	
	public function is_clipboard($fs = null)  
	{
		if(!empty($_SESSION['phpos_clipboard']['id']))
		{
			//if($_SESSION['phpos_clipboard']['fs'] == $fs) return true;	
			return true;	
		}
	}
	 
/*
**************************
*/
 	
	public function is_my_clipboard($fs)
	{
		if($this->is_clipboard() && $fs == 'db_mysql' && $_SESSION['phpos_clipboard']['fs'] == 'db_mysql') 
		{
			return true;	
			
		} elseif($_SESSION['phpos_clipboard']['fs'] != 'db_mysql') {
			
			if($this->is_clipboard() && ($this->is_server() || $_SESSION['phpos_clipboard']['fs'] == $fs)) return true;	
		}						
	}
	 
/*
**************************
*/
 	
	public function debug_clipboard()
	{
		echo '<pre>';

		print_r($_SESSION['phpos_clipboard']['id']);
		print_r($_SESSION['phpos_clipboard']['file_name']);
		
		echo '</pre>';
	}
	
}
?>