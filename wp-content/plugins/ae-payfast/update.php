<?php

//require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('AE_PayFast_Update')){
	class AE_PayFast_Update extends AE_Plugin_Updater{
		const VERSION = '1.1';

		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ae_payfast.php' );
			$this->slug 			= 'ae_payfast';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://update.enginethemes.com/?do=product-update&product=ae_payfast&type=plugin';

			parent::__construct();
		}
	}
	new AE_PayFast_Update();
}


?>