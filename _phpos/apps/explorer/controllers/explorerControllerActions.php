<?php
/*
**********************************

	PHPOS Web Operating system
	MIT License
	(c) 2013 Marcin Szczyglinski
	szczyglis83@gmail.com
	GitHUB: https://github.com/phpos/
	File version: 1.2.8, 2013.10.28
 
**********************************
*/
if(!defined('PHPOS'))	die();	


if(!empty($_FILES)) 
{
		if(is_root() || ($readonly != 1 && globalconfig('disable_upload') !=1 && globalconfig('demo_mode') != 1)) // 0
		{			
			$pathinfo =  pathinfo($_FILES['file']['name']);
			$ext = $pathinfo['extension'];			
			
			$stop_upload = false;
			if(globalconfig('upload_blacklist') != '')
			{
				$blacklist = explode(',', globalconfig('upload_blacklist'));
				if(in_array(strtolower($ext), $blacklist))
				{
					$stop_upload = true;
					$upload_error = 'This filetype is on blacklist';
				}		
					
				} else {
					
					if(globalconfig('upload_whitelist') != '')
					{
						$whitelist = explode(',', globalconfig('upload_whitelist'));
				
						if(!in_array(strtolower($ext), $whitelist))
						{
							$stop_upload = true;
							$upload_error = 'This filetype is not in whitelist';
						}
					}
				
				}
				
/*.............................................. */		
			if(!$stop_upload)
			{
				$_FILES['file']['name'] = filter::fname($_FILES['file']['name']);
				if($phposFS->upload_file($_FILES['file'])) 
				{					
					param('action_status','ok');
					param('action_status_msg', txt('uploaded'));
					cache_param('action_status');	
					cache_param('action_status_msg');					
					//msg::error(txt('access_denied'));											
				}
				
			} else {				
				
				param('action_status','error');
				param('action_status_msg', $upload_error);
				cache_param('action_status');	
				cache_param('action_status_msg');					
				msg::error(txt('access_denied'));				
			}
			
			
		} else {
		
			param('action_status','error');
			param('action_status_msg',txt('access_denied'));
			cache_param('action_status');	
			cache_param('action_status_msg');					
			msg::error(txt('access_denied'));			
		}
		
		unset($_FILES);
	}
	
		
/*.............................................. */	


if(globalconfig('demo_mode') != 1 || is_root())
{

	if(form_submit('new_rename')) 
	{
				if($readonly != 1) // 0
				{
					if($phposFS->rename(strip_tags($_POST['edit_id']), filter::fname($_POST['new_folder_name']))) 
					{
						param('action_status','ok');					
						param('action_status_msg', txt('renamed'));
						cache_param('action_status');	
						cache_param('action_status_msg');	
						msg::ok($txt('updated'));						
					} 					
					
				} else {
				
					param('action_status','error');
					param('action_status_msg',txt('access_denied'));
					cache_param('action_status');	
					cache_param('action_status_msg');					
					msg::error($txt('access_denied'));				
				}
	}
		
		
	
/*.............................................. */	
		
			if(form_submit('new_folder')) 
			{
				if($readonly != 1) // 0
				{
					if($phposFS->new_dir(strip_tags(filter::fname($_POST['new_folder_name'])))) 
					{
						param('action_status','ok');						
						param('action_status_msg', txt('folder_created'));
						cache_param('action_status');	
						cache_param('action_status_msg');	
						msg::ok($txt('created'));
						
					} else {
						
						param('action_status','error');
						param('action_status_msg',txt('folder_create_error'));
						cache_param('action_status');	
						cache_param('action_status_msg');	
						msg::ok($txt('created'));
					}
					
					
				} else {
				
					param('action_status','error');
					param('action_status_msg',txt('access_denied'));
					cache_param('action_status');	
					cache_param('action_status_msg');					
					msg::error($txt('access_denied'));				
				}
			}
			
			
		 
/*
**************************
*/	 
		
		if(!empty($action_id))
		{			
			if(file_exists(PHPOS_DIR.'plugins/filesystems/'.param('fs').'/explorer.actions.php'))
			{
				include PHPOS_DIR.'plugins/filesystems/'.param('fs').'/explorer.actions.php';
			}	
			
		
/*.............................................. */		
		
			switch($action_id)
			{			
				case 'delete':			
					if($phposFS->delete(param('action_param'))) msg::ok(txt('file_deleted'));				
				break;	
				
				
				case 'explorer_link_to_folder':		
					
					$shortcut = new phpos_shortcuts;					
					$shortcut->add(base64_decode(param('action_param2')), 'app', 'explorer', 'index', 'folder_shortcut.png', array('root_id' => param('root_id'), 'workgroup_id' => param('workgroup_id'), 'workgroup_user_id' => param('workgroup_user_id'), 'in_shared' => param('in_shared'),'shared_id' => param('shared_id'),'tmp_shared_id' => param('tmp_shared_id'), 'fs' => 'local_files','dir_id' => base64_decode(param('action_param'))), 'desktop', 0, null);
					echo '<script>phpos.windowRefresh("1", "");</script>';
					msg::ok(txt('updated'));							
					
				break;	
				
/*.............................................. */		
	
				case 'delete_list':		
					$file_hashes = param('action_param');
					if(!empty($file_hashes))
					{
						$e = explode(";;", $file_hashes);
						$c = count($e);
						for($i=0;$i<$c;$i++)
						{
							$phposFS->delete(base64_decode($e[$i]));
						}						
					}						
					msg::ok(txt('file_deleted'));				
				break;	
			
/*.............................................. */			
	
				case 'copy':
					
					$connect_id = null;
					$ftp_id = param('ftp_id');
					if(!empty($ftp_id)) $connect_id = $ftp_id;					
					$phposFS->clipboard_copy();							
					msg::ok(txt('copied_to_clip'));		
					
				break;
			
/*.............................................. */			
	
				case 'copy_multiple':
					
					$connect_id = null;
					$ftp_id = param('ftp_id');
					if(!empty($ftp_id)) $connect_id = $ftp_id;	
					
					$file_hashes = param('action_param');
					param('action_param2', param('fs'));
					
					if(!empty($file_hashes))
					{
						$clipboard = new phpos_clipboard;
						$clipboard->reset_clipboard();
						$clipboard->set_multiple(true);
							
						$e = explode(";;", $file_hashes);
						$c = count($e);
						for($i=0;$i<$c;$i++)
						{							
							param('action_param', base64_decode($e[$i]));								
							$phposFS->clipboard_copy();	
						}	
								
						echo $clipboard->debug_clipboard();
					}					
									
					msg::ok(txt('copied_to_clip'));		
					
				break;
			
/*.............................................. */	

					case 'copy_server':
					
					$connect_id = null;
					$ftp_id = param('ftp_id');
					if(!empty($ftp_id)) $connect_id = $ftp_id;									
					$phposFS->clipboard_copy_server();							
					msg::ok(txt('copied_to_clip'));		
					
				break;
					
/*.............................................. */		

				case 'cut':
					
					$connect_id = null;
					$ftp_id = param('ftp_id');
					if(!empty($ftp_id)) $connect_id = $ftp_id;					
					$phposFS->clipboard_cut();					
					msg::ok(txt('cutted_to_clip'));			
					
				break;		
					
/*.............................................. */		

				case 'cut_multiple':
					
					$connect_id = null;
					$ftp_id = param('ftp_id');
					if(!empty($ftp_id)) $connect_id = $ftp_id;	
					
					$file_hashes = param('action_param');
					param('action_param2', param('fs'));
					
					if(!empty($file_hashes))
					{
						$clipboard = new phpos_clipboard;
						$clipboard->reset_clipboard();
						$clipboard->set_multiple(true);
							
						$e = explode(";;", $file_hashes);
						$c = count($e);
						for($i=0;$i<$c;$i++)
						{							
							param('action_param', base64_decode($e[$i]));								
							$phposFS->clipboard_cut();	
						}	
								
						echo $clipboard->debug_clipboard();
					}					
									
					msg::ok(txt('cutted_to_clip'));	
					
				break;		

			
/*.............................................. */				

				case 'paste':						
				
					$clipboard = new phpos_clipboard;					
					$clipboard->get_clipboard();
					$mode = $clipboard->get_mode();						
					
					if(!$clipboard->is_multiple())
					{
						if($mode == 'copy')
						{						
							if($phposFS->clipboard_paste(param('action_param'), 'copy'))	msg::ok(txt('file_pasted'));	
							echo '<script>phpos.windowRefresh("'.WIN_ID.'", "");</script>';
							
						} elseif($mode == 'cut') {
							
							$source_win = $clipboard->get_source_win();
							if($phposFS->clipboard_paste(param('action_param'), 'cut')) 	
							{
								echo '<script>phpos.windowRefresh("'.$source_win.'", ""); phpos.windowRefresh("'.WIN_ID.'", "");</script>';
								msg::ok(txt('file_pasted'));							
							}
						}
						
					} else {
					
						$clipboard_ids_array = $clipboard->get_file_id();	 
				  	$clipboard_names_array = $clipboard->get_name();	
						$clipboard_fs = $clipboard->get_file_fs();
						$clipboard_source_win = $clipboard->get_source_win();
						$clipboard_connect_id = $clipboard->get_file_connect_id();
					  $clipboard_is_server = $clipboard->is_server();
						
						$c = count($clipboard_ids_array);
						for($i=0; $i<$c; $i++)
						{
							$clipboard->reset_clipboard();
							
							$clipboard->set_mode($mode);
							$clipboard->set_name($clipboard_names_array[$i]);
							$clipboard->set_server($clipboard_is_server);
							$clipboard->set_source_win($clipboard_source_win);
							$clipboard->add_clipboard($clipboard_ids_array[$i], $clipboard_fs, $clipboard_connect_id);	
							
							if($mode == 'copy')
							{						
								$phposFS->clipboard_paste(param('action_param'), 'copy');									
								
							} elseif($mode == 'cut') {
								
								$source_win = $clipboard->get_source_win();
								$phposFS->clipboard_paste(param('action_param'), 'cut');								
							}					
						}
						
						if($mode == 'copy')
						{							
							// Restore clipboard
							$clipboard->reset_clipboard();
							
							$clipboard->set_mode($mode);	
							$clipboard->set_server($clipboard_is_server);
							$clipboard->set_source_win($clipboard_source_win);
							$clipboard->set_multiple(true);
							
							for($i=0; $i<$c; $i++)
							{							
								$clipboard->set_name($clipboard_names_array[$i]);							
								$clipboard->add_clipboard($clipboard_ids_array[$i], $clipboard_fs, $clipboard_connect_id);										
							}
							
							echo '<script>phpos.windowRefresh("'.WIN_ID.'", "");</script>';
							msg::ok(txt('file_pasted'));	
							
						} elseif($mode == 'cut') {					
							
								echo '<script>phpos.windowRefresh("'.$clipboard->set_source_win.'", ""); phpos.windowRefresh("'.WIN_ID.'", "");</script>';
								msg::ok(txt('file_pasted'));							
						}					
					}
					
				break;			
			}	
				
/*.............................................. */	

	
			param('action_id', null);
			cache_param('action_id');
		}

}
		
/*.............................................. */	

	param('action_id', null);
	cache_param('action_id');			
			
	param('action_id', null);
	param('action_param', null);
	cache_param('action_id');
	cache_param('action_param');			
			
?>