<?php
/*
**********************************

	PHPOS Web Operating system
	MIT License
	(c) 2013 Marcin Szczyglinski
	szczyglis83@gmail.com
	GitHUB: https://github.com/phpos/
	File version: 1.0.0, 2013.10.08
 
**********************************
*/
if(!defined('PHPOS'))	die();	



		$link_param = $my_app->get_param('link_param');	
		$location_id = $my_app->get_param('location');
		
		$wincfg['title'] = txt('shortcuts_window_title_new_iframe');
		$wincfg['width'] = '600';
		$wincfg['height'] = '470';		
		$wincfg['back_action'] = 'index';
		$wincfg['back'] = txt('shortcuts_window_back_to_index');	
	
		$button = txt('shortcuts_window_btn_new_mediaframe');
	
	
	if($location_id != 'edit')
	{
		$succ_msg = txt('shortcuts_window_msg_media_created');
	} else {
		$succ_msg = txt('shortcuts_window_msg_media_updated');
	}
	
		$monit_success = "
		phpos.waiting_show();	
		jSuccess(
			'".$succ_msg."',
			{
				autoHide : true, 
				clickOverlay : false,
				MinWidth : 200,
				TimeShown : 5000,
				ShowTimeEffect : 1000,
				HideTimeEffect : 600,
				LongTrip :20,
				HorizontalPosition : 'right',
				VerticalPosition : 'bottom',
				ShowOverlay : false
			}
		);";
	
		$success_code = winclose(WIN_ID).$monit_success;

	
		$app = new phpos_app;
		$app->set_app_id($link_param);
		$app->load_config();
		
	
		$html.= $layout->txtdesc(txt('st_shortcut_medialink'));
		
		$form = new phpos_forms;
		$form->onsuccess($success_code);
		$form->focus('new_link_name');
		$html.= $form->form_start('new_mediaframe', helper_ajax('iframeAction.php'), array('app_params' => ''));
		
	
		$after_reload = $my_app->get_param('after_reload');		
		
		$form->reload_after_submit(array($after_reload));					
					
		$form->input('hidden','new_link_type', '', '',  'app');	
		
		$app = new phpos_app;
		$app->set_app_id('mediaframes');
		$app->load_config();
		
		
		$actions = $app->get_actions();
		$c = count($actions);
		
		if($c != 0)
		{
			$html.= $layout->subtitle(txt('shortcuts_media_type_title'),MY_RESOURCES_URL.'mediaframes.png');
			$html.= $layout->txtdesc(txt('shortcuts_media_type_desc'));		
			$items = array();		
			
			foreach($actions as $key => $data)
			{									
				$items[$key] = $data['name'];							
				$default_value = $app->get_default_action();							
			}		
			
		// if edit
		$link_id = $my_app->get_param('link_id');		
		
		if(!empty($link_id))
		{
			$wincfg['back'] = null;
			$shortcut = new phpos_shortcuts;
			$db_shortcut = $shortcut->get_shortcut($link_id);
			$db_params = $shortcut->get_params_from_db($link_id);	
			$url = base64_decode($db_params['url']);
			$start_link_title = $db_shortcut['file_title'];	
			$button = txt('shortcuts_window_btn_update_mediaframe');
			$wincfg['title'] = txt('shortcuts_window_title_update_mediaframe');			
		}		
		
			
		if(is_array($db_shortcut))
		{
			$default_value = $db_shortcut['app_action'];
		}
			
			$form->select('new_link_action', txt('shortcut_mediaurl_action'), txt('shortcuts_media_type_choose'),  $items, $default_value);	
			$html.= $form->render();	
		}		
		
		
		
		$link_id = $my_app->get_param('link_id');	
		
		if(!empty($link_id))
		{
			$shortcut = new phpos_shortcuts;
			$db_shortcut = $shortcut->get_shortcut($link_id);
			$db_params = $shortcut->get_params_from_db($link_id);	
			$url = base64_decode($db_params['url']);
			$start_link_title = $db_shortcut['file_title'];						
		}					
		
		
		$form->status();
		$html.= $layout->subtitle(txt('shortcuts_media_shortcut_title'),MY_RESOURCES_URL.'mediaframes.png');
		$html.= $layout->txtdesc(txt('shortcuts_media_shortcut_desc'));	
		
		$form->condition('not_null', true, txt('name_empty'));					
		$form->input('text','new_link_name', txt('shortcuts_form_icon_name'), txt('shortcuts_form_icon_name_desc'),  $start_link_title);	
		
		$form->condition('not_null', true, txt('url_empty'));			
		$form->input('text','new_link_url', txt('shortcuts_media_url_title'), txt('shortcuts_media_url_desc'),  $url);			
		
		$icons = new phpos_icons;
		$c = $icons->count_icons();
		$items = array('null' => '---');
		
		if($c != 0)
		{			
			$icons_list = $icons->get_icon_list();
			
			foreach($icons_list as $icon_name)
			{
				$items[$icon_name] = $icon_name;
			}						
		}
		
		$form->title(txt('shortcuts_icon_for_title'), '', MY_RESOURCES_URL.'icon.png');
		$form->select('new_link_icon', txt('shortcuts_icon_for_name'), txt('shortcuts_icon_for_desc'),  $items, $db_shortcut['icon']);
		
	$html.= $form->render();	
	
	$form->submit_btn($button);
	$next_button = $form->render();			
	$html.= $form->form_end();


?>