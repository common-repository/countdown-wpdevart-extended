<?php

class wpdevart_countdown_extended_woocommerce_settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'wpdevart_countdown_extended_woocommerce_settings';
		$this->label = "WpDevArt Countdown Extended";

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		$currency_code_options = get_woocommerce_currencies();

		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')';
		}

		$settings =	array(
				array(
					'title' => 'Countdown Extended ',
					'type'  => 'title',
					'id'    => 'store_address',
				),
				array(
					'title'    => 'Countdown position on product page',
					'desc'     => 'Select where to insert the countdown on the product page',
					'id'       => 'wpdevart_countdown_extended_product_postiton',
					'default'  => 'dont_add',
					'type'     => 'select',
					'class'    => '',
					'desc_tip' => true,
					'options'  => array(
						'dont_add'=>"Disable",
						'woocommerce_before_single_product'         => 'Before product',
						'woocommerce_before_single_product_summary' => 'Before product summary',
						'woocommerce_single_product_summary'        => 'Inside product summary', 
						'woocommerce_after_single_product_summary'  => 'After product summary', 
						'woocommerce_after_single_product'          => 'After product',
						'woocommerce_before_add_to_cart_form'       => 'Before add to cart form',
						'woocommerce_before_add_to_cart_button'     => 'Before add to cart button',
						'woocommerce_after_add_to_cart_button'      => 'After add to cart button',
						'woocommerce_after_add_to_cart_form'        => 'After add to cart form',
					),
				),
				array(
					'title'    => 'Archive/Shop/Category Page position <span style="color:red">(pro)</span>' ,
					'desc'     => 'Select where to insert the countdown on the Archive/Shop/Category page',
					'id'       => 'wpdevart_countdown_extended_shop_position',
					'default'  => 'dont_add',
					'type'     => 'select',
					'class'    => '',
					'desc_tip' => true,
					'options'  => array(
						'dont_add'=>"Disable",
						'woocommerce_before_shop_loop_item'         => 'Before products',
						'woocommerce_before_shop_loop_item_title' 	=> 'Before products image',
						'woocommerce_shop_loop_item_title'        	=> 'After products image', 
						'woocommerce_after_shop_loop_item_title'  	=> 'After products title', 
						'woocommerce_after_shop_loop_item'          => 'After products price',
					),
				),
				array(
					'title'    => 'Enable the Countdown for all products' ,
					'desc'     => 'This option will enable the countdown for all products',
					'id'       => 'woocommerce_enable_timer_in_all_prod',
					'default'  => 'dont_add',
					'type'     => 'select',
					'class'    => '',
					'desc_tip' => true,
					'options'  => array(
						'disable'=>"Disable",
						'enable' => 'Enable',
					),
				),
				array(
					'title'    => 'Select the Countdown for all products' ,
					'desc'     => 'The selected Countdown will be displayed on all products pages. If you want to disable the timer on specific product page, then you need to go to the product page and disable timer or choose another timer from the right side.',
					'id'       => 'wpdevart_countdown_woocommerce_all_timer',
					'default'  => '0',
					'type'     => 'select',
					'class'    => '',
					'desc_tip' => true,
					'options'  => $this->get_timers_array(),
				),
				array(
					'title'    => 'Select the Countdown theme' ,
					'desc'     => 'Set the Countdown theme you want.',
					'id'       => 'wpdevart_countdown_woocommerce_all_theme',
					'default'  => '0',
					'type'     => 'select',
					'class'    => '',
					'desc_tip' => true,
					'options'  => $this->get_themes_array(),
				),
				
				array(
					'type' => 'sectionend',
					'id'   => '',
				),
		);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output a color picker input box.
	 *
	 * @param mixed  $name Name of input.
	 * @param string $id ID of input.
	 * @param mixed  $value Value of input.
	 * @param string $desc (default: '') Description for input.
	 */
	public function color_picker( $name, $id, $value, $desc = '' ) {
		echo '<div class="color_box">' . wc_help_tip( $desc ) . '
			<input name="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
		</div>';
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		$settings = $this->get_settings();
		$prod_pos=get_option('wpdevart_countdown_extended_product_postiton','woocommerce_single_product_summary');
		$shop_pos=get_option('wpdevart_countdown_extended_shop_position','dont_add');
		$enable=get_option('woocommerce_enable_timer_in_all_prod','disable');
		$timer=get_option('wpdevart_countdown_woocommerce_all_timer','dont_add');
		$timer_array=$this->get_timers_array();
		$theme=get_option('wpdevart_countdown_woocommerce_all_theme','dont_add');
		$theme_array=$this->get_themes_array();
		?>
		<div class="wpdevart_plugins_header div-for-clear">
				<div class="wpdevart_plugins_get_pro div-for-clear">
					<div class="wpdevart_plugins_get_pro_info">
						<h3>WpDevArt Countdown Extended Premium</h3>
						<p>Powerful and Customizable Countdown Timer</p>
					</div>
						<a target="blank" href="http://wpdevart.com/wordpress-countdown-extended-version/" class="wpdevart_upgrade">Upgrade</a>
				</div>
				<a target="blank" href="<?php echo wpdevart_countdown_extended_support_url ?>" class="wpdevart_support">Have any Questions? Get quick support!</a>
			</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wpdevart_countdown_extended_product_postiton">Countdown position on product page<span style="color:green"> (Pro Feature!)</span> <span class="woocommerce-help-tip" data-tip="Select where to insert the countdown on the product page"></span></label>
				</th>
				<td class="forminp forminp-select">
					<select onmousedown="wpdevart_pro()"	name="wpdevart_countdown_extended_product_postiton"	id="wpdevart_countdown_extended_product_postiton" style="" class=" wpdevart_pro">
						<option value="dont_add">Disable</option>
						<option <?php selected('woocommerce_before_single_product',$prod_pos) ?> value="woocommerce_before_single_product">Before product</option>
						<option <?php selected('woocommerce_before_single_product_summary',$prod_pos) ?> value="woocommerce_before_single_product_summary">Before product summary</option>
						<option <?php selected('woocommerce_single_product_summary',$prod_pos) ?> value="woocommerce_single_product_summary" >Inside product summary</option>
						<option <?php selected('woocommerce_after_single_product_summary',$prod_pos) ?> value="woocommerce_after_single_product_summary">After product summary</option>
						<option <?php selected('woocommerce_after_single_product',$prod_pos) ?> value="woocommerce_after_single_product">After product</option>
						<option <?php selected('woocommerce_before_add_to_cart_form',$prod_pos) ?> value="woocommerce_before_add_to_cart_form">Before add to cart form</option>
						<option <?php selected('woocommerce_before_add_to_cart_button',$prod_pos) ?> value="woocommerce_before_add_to_cart_button">Before add to cart button</option>
						<option <?php selected('woocommerce_after_add_to_cart_button',$prod_pos) ?> value="woocommerce_after_add_to_cart_button">After add to cart button</option>
						<option <?php selected('woocommerce_after_add_to_cart_form',$prod_pos) ?> value="woocommerce_after_add_to_cart_form">After add to cart form</option>
					</select> 
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wpdevart_countdown_extended_shop_position">Archive/Shop/Category Page position <span style="color:green"> (Pro Feature!)</span><span class="woocommerce-help-tip" data-tip="Select where to insert the countdown on the Archive/Shop/Category page"></span></label>
				</th>
				<td class="forminp forminp-select">
					<select onmousedown="wpdevart_pro()" name="wpdevart_countdown_extended_shop_position" id="wpdevart_countdown_extended_shop_position" style="" class=" wpdevart_pro">
						<option <?php selected('dont_add',$shop_pos) ?> value="dont_add">Disable</option>
						<option <?php selected('woocommerce_before_shop_loop_item',$shop_pos) ?> value="woocommerce_before_shop_loop_item">Before products</option>
						<option <?php selected('woocommerce_before_shop_loop_item_title',$shop_pos) ?> value="woocommerce_before_shop_loop_item_title">Before products image</option>
						<option <?php selected('woocommerce_shop_loop_item_title',$shop_pos) ?> value="woocommerce_shop_loop_item_title">After products image</option>
						<option <?php selected('woocommerce_after_shop_loop_item_title',$shop_pos) ?> value="woocommerce_after_shop_loop_item_title">After products title</option>
						<option <?php selected('woocommerce_after_shop_loop_item',$shop_pos) ?> value="woocommerce_after_shop_loop_item">After products price</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_enable_timer_in_all_prod">Enable the Countdown for all products<span style="color:green"> (Pro Feature!)</span> <span class="woocommerce-help-tip" data-tip="This option will enable the countdown for all products"></span></label>
				</th>
				<td class="forminp forminp-select">
					<select onmousedown="wpdevart_pro()" name="woocommerce_enable_timer_in_all_prod" id="woocommerce_enable_timer_in_all_prod" style="" class=" wpdevart_pro">
					<option <?php selected('disable',$enable) ?> value="disable">Disable</option>
					<option <?php selected('enable',$enable) ?> value="enable">Enable</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wpdevart_countdown_woocommerce_all_timer">Select the Countdown for all products <span style="color:green"> (Pro Feature!)</span> <span class="woocommerce-help-tip" data-tip="The selected Countdown will be displayed on all products pages. If you want to disable the timer on specific product page, then you need to go to the product page and disable timer or choose another timer from the right side."></span></label>
				</th>
				<td class="forminp forminp-select">
					<select onmousedown="wpdevart_pro()" name="wpdevart_countdown_woocommerce_all_timer" id="wpdevart_countdown_woocommerce_all_timer" class=" wpdevart_pro">
						<option value="0" <?php selected($key,'0') ?>>Select Timer</option>
						<?php foreach($timer_array as $key => $value){ ?>
						<option value="<?php echo esc_html($key) ?>" <?php selected($key,$timer) ?>><?php echo esc_html($value); ?></option>
						<?php } ?>						
					</select>
				</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="wpdevart_countdown_woocommerce_all_theme">Select the Countdown theme <span style="color:green"> (Pro Feature!)</span> <span class="woocommerce-help-tip" data-tip="Set the Countdown theme you want."></span></label>
					</th>
					<td class="forminp forminp-select">
						<select onmousedown="wpdevart_pro()" name="wpdevart_countdown_woocommerce_all_theme" id="wpdevart_countdown_woocommerce_all_theme" style="" class=" wpdevart_pro" >
						<?php foreach($theme_array as $key => $value){ ?>
						<option value="<?php echo esc_html($key); ?>" <?php selected($key,$theme) ?>><?php echo esc_html($value); ?></option>
						<?php } ?>
						</select> 
					</td>
				</tr>
		</table>
		<script>
		function wpdevart_pro(){
			alert('If you want to use this feature upgrade to Countdown Pro');
			return false;
		}
		</script>
		<style>
		.form-table th.titledesc {
			width:340px;
		}
			.wpdevart_plugins_header {
				margin: 10px 20px 10px 0;
				width:95%;
			}
			.wpdevart_plugins_get_pro {
				border-radius: 10px;
				background: #ffffff;
				padding: 15px 20px;
				box-sizing: border-box;
				float: left;
				box-shadow: 1px 1px 7px rgba(0,0,0,0.04);
			}
			.wpdevart_plugins_get_pro_info {
				float: left;
				margin-right: 30px;
			}
			.wpdevart_plugins_get_pro_info h3 {
				margin: 0 0 5px 0;
				font-size: 17px;
				font-weight: 500;
			}
			.wpdevart_plugins_get_pro_info p {
				margin: 0;
				font-size: 14px;
				font-weight: 200;
			}
			.wpdevart_support, .wpdevart_upgrade {
				display: inline-block;
				font-size: 16px;
				text-decoration: none;
				border-radius: 5px;
				border: 0;
				color: #ffffff;
				font-weight: 400;
				opacity: 1;
				-webkit-transition: opacity 0.3s;
				-moz-transition: opacity 0.3s;
				transition: opacity 0.3s;
				background-image: linear-gradient(141deg, #32d6db, #00a0d2);
			}
			.wpdevart_upgrade {
				float: left;
				padding: 11px 25px 12px;
				text-transform: uppercase;
			}
			.wpdevart_support {
				float: right;
				padding: 11px 20px 12px 50px;
				margin-top: 15px;
				position: relative;
			}
			.wpdevart_support:before {
				content: "";
				background: url(<?php echo wpda_contdown_extend_plugin_url.'includes/admin/images/support-white.png' ?>) no-repeat;
				width: 25px;
				height: 25px;
				background-size: 25px;
				top: 8px;
				position: absolute;
				left: 15px;
			}
			.div-for-clear:after {
				content: '';
				clear: both;
				display: table;
			}
			.wpdevart_support:hover,
			.wpdevart_upgrade:hover,
			.wpdevart_support:focus,
			.wpdevart_upgrade:focus {
				color:#ffffff;
				opacity:0.85;
				box-shadow: none;
				outline: none;
			}
		</style>
		<?php
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();
		WC_Admin_Settings::save_fields( $settings );
	}
	private function get_timers_array(){
		global $wpdb;
		$timers=$wpdb->get_results('SELECT `id`,`name` FROM ' . wpda_contdown_extend_databese::$table_names['timer']);
		$timers_array=array();
		foreach($timers as $timer){
			$timers_array[$timer->id]=$timer->name;
		}
		return $timers_array;
	}
	private function get_themes_array(){
		global $wpdb;
		$themes=$wpdb->get_results('SELECT `id`,`name` FROM ' . wpda_contdown_extend_databese::$table_names['theme']);
		$themes_array=array();
		foreach($themes as $theme){
			$themes_array[$theme->id]=$theme->name;
		}
		return $themes_array;
	}
}
return new wpdevart_countdown_extended_woocommerce_settings();
?>