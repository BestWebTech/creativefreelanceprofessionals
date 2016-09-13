<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('AE_Mailing_Update')){
	class AE_Mailing_Update extends AE_Plugin_Updater{
		const VERSION = '1.0.0';

		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/et_mailing.php' );
			$this->slug 			= 'et_mailing';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://forum.enginethemes.com/?do=product-update&product='.$this->slug.'&type=plugin';

			parent::__construct();
		}
	}
	new AE_Mailing_Update();
}