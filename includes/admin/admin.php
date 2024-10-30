<?php
class wpda_contdown_extend_admin_panel{
// previus defined admin constants
// wpda_contdown_extend_plugin_url
// wpda_contdown_extend_plugin_path
	private $text_fileds;
	function __construct(){
		$this->admin_filters();
	}

	private function admin_filters(){
		//hook for admin menu
		add_action( 'admin_menu', array($this,'create_admin_menu') );
		/* for post page button*/
		add_filter( 'mce_external_plugins', array( $this ,'mce_external_plugins' ) );
		add_filter( 'mce_buttons', array($this, 'mce_buttons' ) );
		add_action('wp_ajax_wpda_contdown_extend_post_page_content', array($this,"post_page_popup_content"));
		add_action('wp_ajax_countdown_extended_popup_page_save', array($this,"ajax_save_popup_params"));
		// add woocomerce admin filters when it active
		if(wpda_contdown_extend_library::is_plugin_active('woocommerce/woocommerce.php'))
			$this->woocommerce_admin_filters();
		$this->gutenberg();		
	}
	private function woocommerce_admin_filters(){
		// include woocommerce file contain a class and create countdown woocommerce admin class
		require_once(wpda_contdown_extend_plugin_path.'includes/admin/woocommerce_admin.php');
		$wpda_woocommerce=new wpda_contdown_extend_woocomerce();
		// now we can conect hooks with our object functions.
		add_action( 'add_meta_boxes',    array( $wpda_woocommerce, 'add_metabox' ) );
		add_action( 'save_post_product', array( $wpda_woocommerce, 'save_metabox' ));
		/*add settings in settings page*/
		add_filter('woocommerce_get_settings_pages',array($wpda_woocommerce,'woocommerce_settings'));
	}
	//conect admin menu
	public function create_admin_menu(){
		 global $submenu;
		/* conect admin pages to wordpress core*/
		$main_page=add_menu_page( "Countdown", "Countdown", 'manage_options', "wpda_contdown_extend_menu", array($this, 'create_timer_page'),'dashicons-clock');
		add_submenu_page( "wpda_contdown_extend_menu", "Timer", "Timer", 'manage_options',"wpda_contdown_extend_menu",array($this, 'create_timer_page'));
		$countdown_theme=add_submenu_page( "wpda_contdown_extend_menu", "Themes", "Themes", 'manage_options',"wpda_contdown_extend_themes",array($this, 'countdown_themes_page'));		
		$popup_page=$theme_subpage_popup=add_submenu_page( "wpda_contdown_extend_menu", "Popup", "Popup <span style='color:#00ff66' > (Pro Feature!)</span>", 'manage_options',"wpda_contdown_extend_popup",array($this, 'popup_settings_page'));		
		add_submenu_page( "wpda_contdown_extend_menu", "Featured Plugins", "Featured Plugins", 'manage_options',"wpda_contdown_extend_featured_plugins",array($this, 'featured_plugins'));
		 if(isset($submenu['wpda_contdown_extend_menu']))
			add_submenu_page( "wpda_contdown_extend_menu", "Support or Any Ideas?", "<span style='color:#00ff66' >Support or Any Ideas?</span>", 'manage_options',"any_ideas",array($this, 'any_ideas'),155);
		/*for including page styles and scripts*/
		add_action('admin_print_styles-' .$main_page, array($this,'create_timer_page_style_js'));
		add_action('admin_print_styles-' .$countdown_theme, array($this,'create_theme_page_style_js'));		
		add_action('admin_print_styles-' .$popup_page, array($this,'create_popup_page_style_js'));		
		if(isset($submenu['wpda_contdown_extend_menu']))
			$submenu['wpda_contdown_extend_menu'][4][2]=wpdevart_countdown_extended_support_url;
	}
	public function any_ideas(){
		
	}
	/* timer page style and js*/	
	public function create_timer_page_style_js(){
		
		//scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-slider');		
		wp_enqueue_script( 'jquery-ui-spinner');	
		wp_enqueue_script("jquery-ui-date-time-picker-js");
		wp_enqueue_script("jquery-ui-date-time-picker-js");
		wp_enqueue_script('angularejs',wpda_contdown_extend_plugin_url.'includes/admin/js/angular.min.js');
		wp_enqueue_script("wpda_contdown_extend_timer_page_js",wpda_contdown_extend_plugin_url.'includes/admin/js/timer_page.js');

		//styles
		wp_enqueue_style( 'wpdevart-countdown-extended-jquery-ui' );
		wp_enqueue_style('wpda_contdown_extend_timer_page_css',wpda_contdown_extend_plugin_url.'includes/admin/css/timer_page.css');
		wp_enqueue_style('jquery-ui-date-time-picker-css');
				
	}
	
	/* Themes page style and js*/	
	public function create_theme_page_style_js(){
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'wpdevart-countdown-extended-jquery-ui' );
		wp_enqueue_script('jquery-ui-slider');	
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script('angularejs',wpda_contdown_extend_plugin_url.'includes/admin/js/angular.min.js');
		wp_enqueue_style('wpda_contdown_extend_timer_page_css',wpda_contdown_extend_plugin_url.'includes/admin/css/theme_page.css');
		wp_enqueue_script("wpda_contdown_extend_timer_page_js",wpda_contdown_extend_plugin_url.'includes/admin/js/theme_page.js');
	}
	
	/* Popup page style and js*/	
	public function create_popup_page_style_js(){		
		wp_enqueue_style('FontAwesome');
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'wpdevart-countdown-extended-jquery-ui' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_style('wpda_contdown_extend_admin_popup_page_css',wpda_contdown_extend_plugin_url.'includes/admin/css/popup_page.css');
		wp_enqueue_script('wpda_contdown_extend_admin_popup_page_css',wpda_contdown_extend_plugin_url.'includes/admin/js/popup_page.js');
		if (function_exists('wp_enqueue_media')) wp_enqueue_media();
	}
	
	/* Timer page main*/	
	public function create_timer_page(){				
		require_once(wpda_contdown_extend_plugin_path.'includes/admin/coundown_timer_page.php');	
		$timer_page_object=new wpda_contdown_extend_timer_page();
		$timer_page_object->controller_page();	
	}	
	/* themes*/		
	public function countdown_themes_page(){
		require_once(wpda_contdown_extend_plugin_path.'includes/admin/coundown_theme_page.php');	
		$theme_page_objet=new wpda_contdown_extend_theme_page();	
		$theme_page_objet->controller_page();
	}
	
	/* popup page*/
	public function popup_settings_page(){
		require_once(wpda_contdown_extend_plugin_path.'includes/admin/coundown_popup_page.php');	
		$popup_page_objet=new wpda_contdown_extend_popup_page();
		$popup_page_objet->controller_page();
	}	
	
	/*post page button*/
	public function mce_external_plugins( $plugin_array ) {
		$plugin_array["wpda_contdown_extend"] = wpda_contdown_extend_plugin_url.'includes/admin/js/post_page_insert_button.js';
		return $plugin_array;
	}
	/*post page button add_class*/
	public function mce_buttons( $buttons ) {
		array_push( $buttons, "wpda_contdown_extend" );
		return $buttons;
	}
	/*post page button insert in content*/
	public function post_page_popup_content(){	
		require_once(wpda_contdown_extend_plugin_path.'includes/admin/post_page_popup.php');
		$popup_page_objet=new wpda_countdown_post_page_popup();		
	}
	/*ajax saving content*/
	public function ajax_save_popup_params(){	
		require_once(wpda_contdown_extend_plugin_path.'includes/admin/coundown_popup_page.php');
		wpda_contdown_extend_popup_page::save_in_db();
		
	}
	/*concect with gutenberg editor*/
	public function gutenberg(){	
		require_once(wpda_contdown_extend_plugin_path.'includes/admin/gutenberg/gutenberg.php');
		$gutenberg=new wpda_countdown_extended_gutenberg();		
		
	}
	
		/*############################### Featured plugins function ########################################*/
	
	public function featured_plugins(){
		$plugins_array=array(
			'gallery_album'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/gallery-album-icon.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-gallery-plugin',
						'title'			=>	'WordPress Gallery plugin',
						'description'	=>	'Gallery plugin is an useful tool that will help you to create Galleries and Albums. Try our nice Gallery views and awesome animations.'
						),		
			'coming_soon'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/coming_soon.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-coming-soon-plugin/',
						'title'			=>	'Coming soon and Maintenance mode',
						'description'	=>	'Coming soon and Maintenance mode plugin is an awesome tool to show your visitors that you are working on your website to make it better.'
						),
			'Contact forms'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/contact_forms.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-contact-form-plugin/',
						'title'			=>	'Contact Form Builder',
						'description'	=>	'Contact Form Builder plugin is an handy tool for creating different types of contact forms on your WordPress websites.'
						),	
			'Booking Calendar'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/Booking_calendar_featured.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-booking-calendar-plugin/',
						'title'			=>	'WordPress Booking Calendar',
						'description'	=>	'WordPress Booking Calendar plugin is an awesome tool to create a booking system for your website. Create booking calendars in a few minutes.'
						),
			'Pricing Table'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/Pricing-table.png',
						'site_url'		=>	'https://wpdevart.com/wordpress-pricing-table-plugin/',
						'title'			=>	'WordPress Pricing Table',
						'description'	=>	'WordPress Pricing Table plugin is a nice tool for creating beautiful pricing tables. Use WpDevArt pricing table themes and create tables just in a few minutes.'
						),	
			'chart'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/chart-featured.png',
						'site_url'		=>	'https://wpdevart.com/wordpress-organization-chart-plugin/',
						'title'			=>	'WordPress Organization Chart',
						'description'	=>	'WordPress organization chart plugin is a great tool for adding organizational charts to your WordPress websites.'
						),						
			'youtube'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/youtube.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-youtube-embed-plugin',
						'title'			=>	'WordPress YouTube Embed',
						'description'	=>	'YouTube Embed plugin is an convenient tool for adding videos to your website. Use YouTube Embed plugin for adding YouTube videos in posts/pages, widgets.'
						),
            'facebook-comments'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/facebook-comments-icon.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-comments-plugin/',
						'title'			=>	'Wpdevart Social comments',
						'description'	=>	'WordPress Facebook comments plugin will help you to display Facebook Comments on your website. You can use Facebook Comments on your pages/posts.'
						),						
			'countdown'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/countdown.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-countdown-plugin/',
						'title'			=>	'WordPress Countdown plugin',
						'description'	=>	'WordPress Countdown plugin is an nice tool for creating countdown timers for your website posts/pages and widgets.'
						),
			'lightbox'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/lightbox.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-lightbox-plugin',
						'title'			=>	'WordPress Lightbox plugin',
						'description'	=>	'WordPress Lightbox Popup is an high customizable and responsive plugin for displaying images and videos in popup.'
						),
			'facebook'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/facebook.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-like-box-plugin',
						'title'			=>	'Social Like Box',
						'description'	=>	'Facebook like box plugin will help you to display Facebook like box on your wesite, just add Facebook Like box widget to sidebar or insert it into posts/pages and use it.'
						),
			'vertical_menu'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/vertical-menu.png',
						'site_url'		=>	'https://wpdevart.com/wordpress-vertical-menu-plugin/',
						'title'			=>	'WordPress Vertical Menu',
						'description'	=>	'WordPress Vertical Menu is a handy tool for adding nice vertical menus. You can add icons for your website vertical menus using our plugin.'
						),						
			'duplicate_page'=>array(
						'image_url'		=>	wpda_contdown_extend_plugin_url.'includes/admin/images/featured_plugins/featured-duplicate.png',
						'site_url'		=>	'https://wpdevart.com/wordpress-duplicate-page-plugin-easily-clone-posts-and-pages/',
						'title'			=>	'WordPress Duplicate page',
						'description'	=>	'Duplicate Page or Post is a great tool that allows duplicating pages and posts. Now you can do it with one click.'
						),						
						
			
		);
		?>
        <style>
         .featured_plugin_main{
			background-color: #ffffff;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			float: left;
			margin-right: 30px;
			margin-bottom: 30px;
			width: calc((100% - 90px)/3);
			border-radius: 15px;
			box-shadow: 1px 1px 7px rgba(0,0,0,0.04);
			padding: 20px 25px;
			text-align: center;
			-webkit-transition:-webkit-transform 0.3s;
			-moz-transition:-moz-transform 0.3s;
			transition:transform 0.3s;   
			-webkit-transform: translateY(0);
			-moz-transform: translateY(0);
			transform: translateY(0);
			min-height: 344px;
		 }
		.featured_plugin_main:hover{
			-webkit-transform: translateY(-2px);
			-moz-transform: translateY(-2px);
			transform: translateY(-2px);
		 }
		.featured_plugin_image{
			max-width: 128px;
			margin: 0 auto;
		}
		.blue_button{
    display: inline-block;
    font-size: 15px;
    text-decoration: none;
    border-radius: 5px;
    color: #ffffff;
    font-weight: 400;
    opacity: 1;
    -webkit-transition: opacity 0.3s;
    -moz-transition: opacity 0.3s;
    transition: opacity 0.3s;
    background-color: #7052fb;
    padding: 10px 22px;
    text-transform: uppercase;
		}
		.blue_button:hover,
		.blue_button:focus {
			color:#ffffff;
			box-shadow: none;
			outline: none;
		}
		.featured_plugin_image img{
			max-width: 100%;
		}
		.featured_plugin_image a{
		  display: inline-block;
		}
		.featured_plugin_information{	

		}
		.featured_plugin_title{
	color: #7052fb;
	font-size: 18px;
	display: inline-block;
		}
		.featured_plugin_title a{
	text-decoration:none;
	font-size: 19px;
    line-height: 22px;
	color: #7052fb;
					
		}
		.featured_plugin_title h4{
			margin: 0px;
			margin-top: 20px;		
			min-height: 44px;	
		}
		.featured_plugin_description{
			font-size: 14px;
				min-height: 63px;
		}
		@media screen and (max-width: 1460px){
			.featured_plugin_main {
				margin-right: 20px;
				margin-bottom: 20px;
				width: calc((100% - 60px)/3);
				padding: 20px 10px;
			}
			.featured_plugin_description {
				font-size: 13px;
				min-height: 63px;
			}
		}
		@media screen and (max-width: 1279px){
			.featured_plugin_main {
				width: calc((100% - 60px)/2);
				padding: 20px 20px;
				min-height: 363px;
			}	
		}
		@media screen and (max-width: 768px){
			.featured_plugin_main {
				width: calc(100% - 30px);
				padding: 20px 20px;
				min-height: auto;
				margin: 0 auto 20px;
				float: none;
			}	
			.featured_plugin_title h4{
				min-height: auto;
			}	
			.featured_plugin_description{
				min-height: auto;
					font-size: 14px;
			}	
		}

        </style>
      
		<h1 style="text-align: center;font-size: 50px;font-weight: 700;color: #2b2350;margin: 20px auto 25px;line-height: 1.2;">Featured Plugins</h1>
		<?php foreach($plugins_array as $key=>$plugin) { ?>
		<div class="featured_plugin_main">
			<div class="featured_plugin_image"><a target="_blank" href="<?php echo esc_url($plugin['site_url']); ?>"><img src="<?php echo esc_url($plugin['image_url']); ?>"></a></div>
			<div class="featured_plugin_information">
				<div class="featured_plugin_title"><h4><a target="_blank" href="<?php echo $plugin['site_url'] ?>"><?php echo esc_html($plugin['title']); ?></a></h4></div>
				<p class="featured_plugin_description"><?php echo esc_html($plugin['description']); ?></p>
				<a target="_blank" href="<?php echo esc_url($plugin['site_url']); ?>" class="blue_button">Check The Plugin</a>
			</div>
			<div style="clear:both"></div>                
		</div>
		<?php } 
	
	}
	
}
?>
