<?php
/*WpDevart Coundown Extended woocommerce class*/
class wpda_contdown_extend_woocomerce{
	
	function __construct(){
		
	}
	public function add_metabox(){
		add_meta_box('wpda_countdown_extended_woocommerce_meta', 'WpDevArt Countdown Extended', array( $this, 'display_metabox' ), 'product', 'side', 'high');
	}
	public function display_metabox(){
		$id = get_the_ID();
		$enable = get_post_meta( $id, 'wpda_countdown_extended_enable', true );
	
		$timer = get_post_meta( $id, 'wpda_countdown_extended_timer', true );
		$theme = get_post_meta( $id, 'wpda_countdown_extended_theme', true );
		echo $this->create_enable_timer($enable);
		echo $this->create_timer_select($timer);
		echo $this->create_theme_select($theme);
		
	}
	
	public function save_metabox(){
		$id = get_the_ID();
		if(isset($_POST[ 'wpdevart_countdown_extended_timer']) && isset($_POST[ 'wpdevart_countdown_extended_theme']) && isset($_POST[ 'wpdevart_countdown_extended_enable'])){
			update_post_meta( $id, 'wpda_countdown_extended_enable', intval( $_POST[ 'wpdevart_countdown_extended_enable'] ));
			update_post_meta( $id, 'wpda_countdown_extended_timer', intval( $_POST[ 'wpdevart_countdown_extended_timer'] ));
			update_post_meta( $id, 'wpda_countdown_extended_theme', intval( $_POST[ 'wpdevart_countdown_extended_theme'] ));
		}		
	}
	private function create_enable_timer($enable_timer){		
		global $wpdb;
		$label='<div style="margin-bottom: 5px;"><label>Enable timer:</label></div>';
		$select='<select style="width: 180px;" class="select" name="wpdevart_countdown_extended_enable" id="wpdevart_countdown_extended_enable">';
		$select.='<option '.selected($enable_timer,'1',false).' value="1">Enable</option>';
		$select.='<option '.selected($enable_timer,'0',false).' value="0">Disable</option>';
		$select.='</select>';
		return $label.$select;
	}
	private function create_timer_select($current_timer){		
		global $wpdb;
		$timers=$wpdb->get_results('SELECT `id`,`name` FROM ' . wpda_contdown_extend_databese::$table_names['timer']);
		$label='<div style="margin-bottom: 5px;"><label>Select timer:</label></div>';
		$select='<select style="width: 180px;" class="select" name="wpdevart_countdown_extended_timer" id="wpdevart_countdown_extended_timer">';
		$select.='<option '.selected($current_timer,'0',false).' value="0">Select Timer</option>';
		foreach($timers as $timer){
			$select.='<option '.selected($current_timer,$timer->id,false).' value="'.esc_attr($timer->id).'">'.esc_html($timer->name).'</option>';
		}
		$select.='</select><a class="add-new-h2" target="_blank" style="font-size: 11px;top: 0px;" href="'.get_admin_url().'admin.php?page=wpda_contdown_extend_menu&task=add_wpda_contdown_extend_timer"> Add new</a>';
		return $label.$select;
	}
	private function create_theme_select($current_theme){
		global $wpdb;
		$themes=$wpdb->get_results('SELECT `id`,`name` FROM ' . wpda_contdown_extend_databese::$table_names['theme']);
		$label='<div style="margin-bottom: 5px;"><label>Select theme:</label></div>';
		$select='<select style="width: 180px;" class="select" name="wpdevart_countdown_extended_theme" id="wpdevart_countdown_extended_theme">';
		foreach($themes as $theme){
			$select.='<option '.selected($current_theme,$theme->id,false).' value="'.esc_attr($theme->id).'">'.esc_html($theme->name).'</option>';
		}
		$select.='</select><a class="add-new-h2" target="_blank" style="font-size: 11px;top: 0px;" href="'.esc_url(get_admin_url()).'admin.php?page=wpda_contdown_extend_themes&task=add_wpda_contdown_extend_theme"> Add new</a>';
		return $label.$select;
	}
	public function woocommerce_settings($settings){
		$settings[] = include wpda_contdown_extend_plugin_path.'includes/admin/woocommerce_settings_tab.php';
		return $settings;
	}
}


?>