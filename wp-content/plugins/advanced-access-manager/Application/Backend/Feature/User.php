<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * User view manager
 * 
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class AAM_Backend_Feature_User {
    
    /**
     * Constructor
     * 
     * @return void
     * 
     * @access public
     * @throws Exception
     */
    public function __construct() {
        $cap = AAM_Core_Config::get('page.capability', 'administrator');
        if (!AAM::getUser()->hasCapability($cap)) {
            Throw new Exception(__('Accedd Denied', AAM_KEY));
        }
    }

    /**
     * Retrieve list of users
     * 
     * Based on filters, get list of users
     * 
     * @return string JSON encoded list of users
     * 
     * @access public
     */
    public function getTable() {
        //get total number of users
        $total = count_users();
        $result = $this->query();
        
        $response = array(
            'recordsTotal' => $total['total_users'],
            'recordsFiltered' => $result->get_total(),
            'draw' => AAM_Core_Request::request('draw'),
            'data' => array(),
        );
        
        foreach ($result->get_results() as $user) {
            $response['data'][] = array(
                $user->ID,
                implode(', ', $this->getUserRoles($user->roles)),
                ($user->display_name ? $user->display_name : $user->user_nicename),
                implode(',', $this->prepareRowActions($user))
            );
        }

        return json_encode($response);
    }
    
    /**
     * Get list of user roles
     * 
     * @param array $roles
     * 
     * @return array
     * 
     * @access protected
     */
    protected function getUserRoles($roles) {
        $response = array();
        
        $names = AAM_Core_API::getRoles()->get_names();
        
        foreach($roles as $role) {
            if (isset($names[$role])) {
                $response[] = translate_user_role($names[$role]);
            }
        }
        
        return $response;
    }
    
    /**
     * Prepare user row actions
     * 
     * @param WP_User $user
     * 
     * @return array
     * 
     * @access protected
     */
    protected function prepareRowActions(WP_User $user) {
        $max = AAM_Core_API::maxLevel(wp_get_current_user()->allcaps);
        
        if ($max < AAM_Core_API::maxLevel($user->allcaps)) {
            $actions = array('no-manage', 'no-lock', 'no-edit');
        } else {
            $actions = array('manage');

            $prefix = ($user->ID == get_current_user_id() ? 'no-' : '');
            
            $actions[] = $prefix . ($user->user_status ? 'unlock' : 'lock');
            $actions[] = 'edit';
        }
        
        if (class_exists('user_switching')) {
            $url = user_switching::maybe_switch_url($user);
            
            if (!in_array('edit', $actions) || empty($url)) {
                $actions[] = 'no-switch';
            } else {
                $actions[] = 'switch|' . $url;
            }
        }
        
        return $actions;
    }

    /**
     * Query database for list of users
     * 
     * Based on filters and settings get the list of users from database
     * 
     * @return \WP_User_Query
     * 
     * @access public
     */
    public function query() {
        $search = trim(AAM_Core_Request::request('search.value'));
        
        $args = array(
            'number' => '',
            'blog_id' => get_current_blog_id(),
            'role' => AAM_Core_Request::request('role'),
            'fields' => 'all',
            'number' => AAM_Core_Request::request('length'),
            'offset' => AAM_Core_Request::request('start'),
            'search' => ($search ? $search . '*' : ''),
            'search_columns' => array(
                'user_login', 'user_email', 'display_name'
            ),
            'orderby' => 'user_nicename',
            'order' => 'ASC'
        );

        return new WP_User_Query($args);
    }

    /**
     * Block user
     * 
     * @return string
     * 
     * @access public
     */
    public function block() {
        $subject = AAM_Backend_View::getSubject();

        //user is not allowed to lock himself
        if ($subject->getId() != get_current_user_id()) {
            $result = $subject->block();
        } else {
            $result = false;
        }

        return json_encode(
                array('status' => ($result ? 'success' : 'failure'))
        );
    }

}