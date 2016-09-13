<?php namespace Gewora\AccountLockerLite;
/*======================================================================*\
|| #################################################################### ||
|| #             This file is part of Account Locker Lite             # ||
|| # ---------------------------------------------------------------- # ||
|| #         Copyright © 2014 Gewora.net. All Rights Reserved.        # ||
|| # This file may not be redistributed in whole or significant part  # ||
|| #                                                                  # ||
|| #            For more license information contact Gewora           # ||
|| #            and/or read the license details which have            # ||
|| #                  been included in this product                   # ||
|| #                                                                  # ||
|| # You are NOT allowed to modify/remove this copyright information  # ||
|| #                                                                  # ||
|| #              Any infringement of this copyright will             # ||
|| #               result in legal action by the holder               # ||
|| #                                                                  # ||
|| # -------------------- http://www.gewora.net --------------------- # ||
|| #################################################################### ||
\*======================================================================*/

class Gewora
{
    public function __construct()
    {
        $this->initialize();
    }

    public function initialize()
    {
        add_action('edit_user_profile', array($this, 'output_lock_status_options'));
        add_action('edit_user_profile_update', array($this, 'save_lock_status_options'));
        add_filter('authenticate', array($this, 'user_authentication'), 9999);
        add_filter('allow_password_reset', array($this, 'allow_password_reset'), 9999, 2);
    }

    private function fetch_lock_status($user_id) {
        return  Get_User_Meta($user_id, 'account_locked', TRUE);
    }

    public function output_lock_status_options($user){
        $locking_data = $this->fetch_lock_status($user->data->ID);
        require_once GEWORA_ACCOUNT_LOCKER_LITE_WORDPRESS_PLUGIN_BASE_PATH . 'templates/output_lock_status_options.php';
    }

    public function save_lock_status_options($user_id)
    {
        if(isset($_POST['account_status'])){
            if($_POST['account_status'] == 'locked') {
                $this->lock_account($user_id);
            } else {
                $this->unlock_account($user_id);
            }
        }
    }

    public function lock_account($user_id){
        Update_User_Meta($user_id, 'account_locked', TRUE);

        do_action('lock_account', $user_id);
    }

    public function unlock_account($user_id){
        Delete_User_Meta($user_id, 'account_locked');
        Do_Action('unlock_account', $user_id);
    }

    public function user_authentication($user) {
        $status = get_class($user);
        if($status == 'WP_User') {
            $locking_data = $this->fetch_lock_status($user->data->ID);

            if($locking_data) {
                $message = Apply_Filters('account_lock_message', SPrintF('<strong>%s</strong> %s', 'Error:', 'Your account has been locked. '), $user);
                return new \WP_Error('authentication_failed', $message);
            } else {
                return $user;
            }
        }
        return $user;
    }

    function Allow_Password_Reset($status, $user_id){
        $locking_data = $this->fetch_lock_status($user_id);
        if($locking_data) {
            return FALSE;
        } else {
            return $status;
        }
    }
}