<?php

//require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('AE_Sagepay_Update') && class_exists('AE_Plugin_Updater') ){
	class AE_Sagepay_Update extends AE_Plugin_Updater{
		const VERSION = '1.1';

		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ae_sagepay.php' );
			$this->slug 			= 'ae_sagepay';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://update.enginethemes.com/?do=product-update&product=ae_sagepay&type=plugin';

			parent::__construct();
		}
	}
	new AE_Sagepay_Update();
}


?>