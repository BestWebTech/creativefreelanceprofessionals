<?php
/**
 * Private message action class
 */
class AE_Private_Message_Actions extends AE_PostAction
{
    public static $instance;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * The constructor
     *
     * @param void
     * @return void
     * @since 1.0
     * @author Tambh
     */
    public function __construct() {
     $this->post_type = 'ae_private_message';
    }
    /**
      * Init for class AE_Private_Message_Actions
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function init()
    {
        $this->add_action('ae_bid_item_template', 'ae_private_message_add_button', 10, 2);
        $this->add_action( 'wp_footer', 'ae_private_message_add_template' );
        $this->add_action( 'fre_profile_tabs', 'ae_private_message_add_profile_tab');
        $this->add_action( 'fre_profile_tabs_on_mobile', 'ae_private_message_add_profile_tab_on_mobile');
        $this->add_action( 'fre_profile_tab_content', 'ae_private_message_add_profile_tab_content');

        $this->add_filter( 'ae_convert_ae_private_message', 'ae_private_message_convert');
        $this->add_filter('ae_fetch_ae_private_message_args', 'ae_private_message_filter_args', 10, 2);
        $this->add_filter('posts_join','ae_private_message_add_join');
        $this->add_filter('posts_search','ae_private_message_alter_search',10, 2);
        $this->add_filter( 'posts_groupby', 'ae_private_message_posts_groupby', 10, 2 );
        $this->add_filter( 'fre_notify_item', 'ae_private_message_notify_item', 10, 2);
        $this->add_action( 'fre_header_before_notify', 'ae_private_message_add_notification_menu');
        $this->add_filter( 'posts_where' , 'ae_private_message_posts_where', 10, 2);

       // $this->add_ajax( 'ae-fetch-replies', 'ae_private_message_fetch_replies');
        $this->add_ajax( 'ae-fetch-conversation', 'fetch_post');
        $this->add_ajax( 'ae-sync-ae_private_message', 'ae_private_message_sync');
        $this->add_ajax( 'ae-private-message-get-replies', 'ae_private_message_get_replies_info');
        $this->add_ajax( 'ae-private-message-get-unread', 'ae_private_message_get_unread');
        $this->add_action( 'ae_login_user', 'ae_private_message_update_unread');

    }
    /**
      * Private message button
      * @param object $bid
      * @param object $project
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function  ae_private_message_add_button($bid, $project){
        ae_private_message_button( $bid,$project );
    }
    /**
      * Add private message modal
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public  function ae_private_message_add_template(){
        ae_private_message_modal();
        ae_private_message_loop_item();
        ae_private_message_redirect();
        ae_private_message_reply_loop_item();
    }
    /**
      * Sync private message data
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_sync(){
        $request = $_REQUEST;
        if( isset($request['sync_type']) && $request['sync_type'] == 'reply'){
            unset($request['sync_type']);
            $this->ae_private_message_sync_reply($request);
        }
        else{
            $this->ae_private_message_sync_conversation($request);
        }

    }
    /**
      * Validate data
      * @param array $data
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_validate( $data ){
        global $user_ID;
        if( !empty($data)){
            if( isset($data['method']) && $data['method'] == 'create' ) {
                //check time delay
                $response = $this->ae_private_message_check_time_delay();
                if (!$response['success']) {
                    return $response;
                }
                $response = ae_private_message_created_a_conversation($data);
                if (!$response['success']) {
                    return $response;
                }
                //check sender can send a message
                if (isset($data['from_user']) && $data['project_id']) {
                    $response = $this->ae_private_message_authorize_sender($data['from_user'], $data['project_id'], $data);
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => __("Your account can't send this message!", ET_DOMAIN)
                    );
                    return $response;
                }
                // check receiver can receive a message
                if (isset($data['to_user']) && $data['bid_id']) {
                    $response = $this->ae_private_message_authorize_receiver($data['to_user'], $data['bid_id'], $data);
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => __("Your account can't send this message!", ET_DOMAIN)
                    );
                    return $response;
                }
                //check message content
                $response = $this->ae_private_message_authorize_message($data);
            }
        }
        else{
            $response = array(
                'success' => false,
                'msg' => __('Data is empty!', ET_DOMAIN)
            );
        }
        return $response;
    }
    /**
      * authorize sender
      * @param integer $user_id
      * @param integer $project_id
      * @param array $data
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_authorize_sender( $user_id, $project_id, $data ){
        global $user_ID;
        if( $user_id == $user_ID ) {
            if (is_project_owner($user_ID, $project_id)) {
                $response = array(
                    'success' => true,
                    'msg' => __("Authorize successful", ET_DOMAIN),
                    'data'=> $data
                );
                return $response;
            }
        }
        $response = array(
            'success' => false,
            'msg' => __("Your account can't send this message!", ET_DOMAIN)
        );
        return $response;
    }
    /**
     * authorize receiver
     * @param integer $user_id
     * @param integer $bid_id
     * @param array $data
     * @return array $response
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_authorize_receiver( $user_id, $bid_id, $data ){
        global $user_ID;
        if( $user_id == $user_ID ) {
            if (is_bid_owner($user_ID, $bid_id)) {
                $response = array(
                    'success' => true,
                    'msg' => __("Authorize successful", ET_DOMAIN),
                    'data'=> $data
                );
                return $response;
            }
        }
        $response = array(
            'success' => false,
            'msg' => __("Your account can't send this message!", ET_DOMAIN)
        );
        return $response;
    }
    /**
     * authorize message content
     * @param array $data
     * @return array $response
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_authorize_message( $data ){
        if( isset($data['post_title']) &&  $data['post_title'] !== '' ){
            $response = array(
                'success' => true,
                'msg' => __("This title is valid!", ET_DOMAIN),
                'data'=> $data
            );
        }
        else{
            $response = array(
                'success' => false,
                'msg' => __("Please enter your message's subject!", ET_DOMAIN)
            );
            return $response;
        }
        if( isset($data['post_content']) &&  $data['post_content'] != '' ){
            $response = array(
                'success' => true,
                'msg' => __("This content is valid!", ET_DOMAIN),
                'data'=> $data
            );
        }
        else{
            $response = array(
                'success' => false,
                'msg' => __("Please enter your message's content!", ET_DOMAIN)
            );
            return $response;
        }
        return $response;
    }
    /**
     * Send message notification to freelancer
     * @param string|array $user_email
     * @param string $subject
     * @param string $message
     * @param array $filter
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_email_notification( $user_email, $subject, $message, $filter ){
        $ae_mailing = AE_Mailing::get_instance();
        $ae_mailing->wp_mail($user_email, $subject, $message, $filter);

    }
    /**
     * Filter message content
     * @param object $result
     * @return string $message
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_get_email_message($result){
      $message = ae_get_option('ae_private_message_mail_template');
      $message = str_ireplace('[from_user]', ae_get_user_display_name( $result->from_user ) , $message);
      //$profile = ae_private_msg_user_profile( $result->to_user );
      $profile_link = et_get_page_link('profile');
      $message_link = '<a href="'.$profile_link.'#tab_private_msg">here</a>';
      $message = str_ireplace('[message_link]', $message_link , $message);
      $message = str_ireplace('[private_message]', $result->post_content,$message);
      return $message;
    }
    /**
      * Check time delay
      * @param string $type conversation | reply
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_check_time_delay($type = 'conversation'){
        global $user_ID;
        $response = array(
            'success' => true
        );
        if( $type == 'reply') {
            $time_delay = (int)ae_get_option('ae_private_message_reply_time_delay', 0);
            if ($time_delay > 0) {
                $user_latest = get_user_meta($user_ID, 'ae_private_message_reply_latest', true);
                $time_dis = 0;
                if ($user_latest) {
                    $time_dis = time() - $user_latest;
                }
                if ($time_dis < (int)$time_delay && $user_latest > 0) {
                    $response = array(
                        'success' => false,
                        'msg' => __("Please wait a few second for your next message!", ET_DOMAIN)
                    );
                }
            }
        }
        else{
            $time_delay = (int)ae_get_option('ae_private_message_time_delay', 0);
            if ($time_delay > 0) {
                $user_latest = get_user_meta($user_ID, 'ae_private_message_latest', true);
                $time_dis = 0;
                if ($user_latest) {
                    $time_dis = time() - $user_latest;
                }
                if ($time_dis < (int)$time_delay && $user_latest > 0) {
                    $response = array(
                        'success' => false,
                        'msg' => __("Please wait a few second for your next message!", ET_DOMAIN)
                    );
                }
            }
        }
        return $response;
    }
    /**
     * add profile tab
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_add_profile_tab(){
      ae_private_message_add_profile_tab_template();
    }
    public function ae_private_message_add_profile_tab_on_mobile(){
      ae_private_message_add_profile_tab_template_on_mobile();
    }
    /**
     * add profile tab content
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_add_profile_tab_content(){
      ae_private_message_add_profile_tab_content_template();
    }
    /**
      * Convert private message
      * @param object $result
      * @return object $result
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_convert($result){
        $result->project_name = '';
        $project = get_post( $result->project_id);
        if( isset($project->post_title)  ){
            $result->project_name = $project->post_title;
        }
        $author = $this->ae_private_message_get_author($result);
        $result->conversation_author_name = '';
        $result->conversation_author_url = '';
        $result->conversation_author_avatar = '';
        if( $author ){
          $result->conversation_author_name = $author->display_name;
          $result->conversation_author_url = $author->author_url;
          $result->conversation_author_avatar = get_avatar( $author->ID, 70);
        }
        $post_date = get_post_time('Y-m-d h:i:s', true, $result->ID);
        $result->post_author_avatar = get_avatar( $result->post_author, 40);
        if( $result->last_date == ''){
            $result->last_date = $post_date;
        }
        $result->last_date = sprintf( _x( '%s ago', '%s = human-readable time difference', ET_DOMAIN ), human_time_diff( strtotime($result->last_date), current_time( 'timestamp' ) ));
        $result->conversation_date = sprintf( _x( '%s ago', '%s = human-readable time difference', ET_DOMAIN ), human_time_diff( strtotime($post_date), current_time( 'timestamp' ) ));
        $result->conversation_latest_reply = $this->ae_private_message_latest_reply($result);

        return $result;
    }
    /**
      * Get conversation author
      * @param array $post
      * @return array $author
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */

    public function  ae_private_message_get_author($postr){
        global $current_user, $user_ID;
        $ae_user = AE_Users::get_instance();
        $user = $ae_user->convert($current_user->data);
        if( $postr->is_conversation == 1){
          if( $user_ID == $postr->post_author){
               $user = get_userdata($postr->to_user);
          }
          else{
              $user = get_userdata($postr->post_author);
           }
         }
        return $user;
    }
    /**
      * Filter args when fetch data
      * @param array $query_args
      * @return array $query_args
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_filter_args( $query_args ){
		global $user_ID;
        extract($_REQUEST);

        if( isset($query['fetch_type']) && $query['fetch_type'] = 'replies' ){
            $args = array('post_parent'=> $query['post_parent']);
            $args = ae_private_message_replies_default_query_args($args);
            $query_args = wp_parse_args($args, $query_args);
        }
        else {
            if (isset($query['post_status']) && $query['post_status'] == 'unread') {
                $query_args = wp_parse_args(array('post_status' => array('unread')), $query_args);
            } else {
                $query_args = wp_parse_args(array('post_status' => array('publish', 'unread')), $query_args);
            }
            $query_args = ae_private_message_default_query_args($query_args);
            if (isset($query['conversation_status']) && $query['conversation_status'] == 'unread') {
                $meta_query = array(
                    array(
                        'key'=> 'conversation_status',
                        'value'=> 'unread'
                    ));
                $query_args['meta_query'] = wp_parse_args($meta_query, $query_args['meta_query']);
            }
        }
        return $query_args;
    }
    /**
      * add more join
      * @param string $joins
      * @return string $joins
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_add_join($joins){
        if( empty($_REQUEST) ){
            return $joins;
        }
        extract($_REQUEST);
        if( isset($query['s']) && $query['s'] != '') {
            $joins = $this->ae_private_message_join_clauses($joins);
        }
        return $joins;
    }
    /**
      * Search conversation by project title
      * @param string $search
      * @param mixed $query
      * @return string search
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_alter_search( $search, $qry){
      global $user_ID;
        $s = $qry->get('s');
        if( $s != '' && $qry->get('post_type') == 'ae_private_message') {
            $add = $this->ae_private_message_search_main_where_clauses($s);
            $pat = '|\(\((.+)\)\)|';
            $search = preg_replace($pat, '((($1) ' . $add . '))', $search);
        }
        return $search;
    }
    /**
      * Group by post id
      * @param string $groupby
      * @param mixed $query
      * @return string $groupby
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_posts_groupby($groupby, $query) {
        if( $query->get('s') != '' && $query->get('post_type') == 'ae_private_message') {
            global $wpdb;
            $groupby = "{$wpdb->posts}.ID";
        }
        return $groupby;
    }
    public function ae_private_message_set_archive_on_receiver($request, $value){
        $request['archive_on_receiver'] = $value;
    }
    /**
      * Check current user can archive this message on their board
      * @param array $data
      * @return string sender if is employer, receiver if is freelancer and none if else
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_archive_message($data){
        global $user_ID;
        if( $user_ID == (int)$data['post_author'] ){
            $data['archive_on_sender'] = 1;
        }
        else if( $user_ID == (int)$data['to_user'] ){
            $data['archive_on_receiver'] = 1;
        }
        else{
            return $data;
        }
        return $data;
    }
    /**
      * Private message join query for freelancer
      * @param string $joins
      * @return string $joins
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_join_clauses( $joins ){
        global $user_ID, $wpdb;
        $joins .= " INNER JOIN {$wpdb->postmeta} AS gmt1 ON ({$wpdb->posts}.ID = gmt1.post_id)";
        return $joins;
    }
    /**
      * private message main where clause for freelancer
      * @param void
      * @return string $add1
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public  function ae_private_message_freelancer_main_where_clauses(){
        global $wpdb, $user_ID;
        $add1 = '';
        if( ae_user_role($user_ID) == FREELANCER ){
            $add1 = $wpdb->prepare("((fmt1.meta_key = 'to_user' AND CAST(fmt1.meta_value AS CHAR) LIKE '%%%s%%') AND (fmt2.meta_key = 'archive_on_receiver' AND CAST(fmt2.meta_value AS CHAR) LIKE '%%%s%%'))", $user_ID, 0);
        }
        return $add1;
    }
    /**
     * private message main where clause for employer
     * @param void
     * @return string $add
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public  function ae_private_message_employer_main_where_clauses(){
        global $wpdb, $user_ID;
        $add = '';
        if( ae_user_role($user_ID) == EMPLOYER ){
            $add = $wpdb->prepare(" (emt1.meta_key = 'archive_on_sender' AND CAST(emt1.meta_value AS CHAR) LIKE '%%%s%%')", 0);
        }
        return $add;
    }
    /**
     * private message main where clause for all user
     * @param void
     * @return string $add
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public  function ae_private_message_all_main_where_clauses(){
        global $wpdb;
        $add = '';
        $add = $wpdb->prepare(" (gmt1.meta_key = 'is_conversation' AND CAST(gmt1.meta_value AS CHAR) LIKE '%%%s%%')", 1);
        return $add;
    }
    /**
     * private message main search by keywords clause
     * @param string $s
     * @return string $add
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public  function ae_private_message_search_main_where_clauses( $s ){
        global $wpdb;
        $add = '';
        $ae_search = AE_Search::getInstance();
        $s_arr = $ae_search->ae_parse_search($s);
        $csearchor = 'OR';
        foreach ( $s_arr as $term ) {
            $like = '%' . $wpdb->esc_like( $term ) . '%';
            $add .= $wpdb->prepare("{$csearchor}(gmt1.meta_key = 'project_name' AND CAST(gmt1.meta_value AS CHAR) LIKE %s )", $like);
        }
        return $add;

    }
    /**
      * check current can update this conversation
      * @param array $conversation
      * @return bool true if user can update conversation false if can't
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_user_can_update_conversation($conversation){
        global $user_ID;
        if( !$user_ID ){
            return false;
        }
        $user_role = ae_user_role($user_ID);
        if(  $user_role == 'administrator' ||  $user_role == EMPLOYER ){
            if( $user_ID == $conversation['post_author'] ) {
                return true;
            }
        }
        if(  $user_role == FREELANCER ){
            if( $user_ID == $conversation['to_user'] ) {
                return true;
            }
        }
        return false;
    }
    /**
      * notication
      * @param object $private_message
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_notification($private_message){
        global $user_ID;
        $content = 'type=new_private_message&project=' . $private_message->project_id . '&sender=' . $user_ID;
        $notification = array(
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_status' => 'publish',
            'post_type' => 'notify',
            'post_title' => sprintf(__("New private message on project %s ", ET_DOMAIN) , get_the_title($private_message->ID)) ,
            'post_parent' => $private_message->ID
        );

        // update notify for freelancer if current user is project owner
        if ($user_ID == $private_message->post_author) {
            $notification['post_author'] = $private_message->to_user;
        }

        // update notify to employer if freelancer send message
        if ($user_ID == $private_message->to_user) {
            $notification['post_author'] = $private_message->post_author;
        }

        $have_new = get_post_meta( $private_message->ID, $user_ID.'_new_private_message', true );

        if($have_new) return ;
        update_post_meta( $private_message->ID, $user_ID.'_new_private_message', true );
        if( class_exists('Fre_Notification') ) {
            $fre_noti = Fre_Notification::getInstance();
            $noti = $fre_noti->insert($notification);
            if($noti){
                ae_private_message_update_new_number( $notification['post_author']);
            }
            //return $noti;
        }
    }
    public function ae_private_message_notify_item( $content, $notify ){
        $post_excerpt = str_replace('&amp;', '&', $notify->post_excerpt);
        parse_str($post_excerpt);
        if (!isset($type) || !$type) return 0;
       // var_dump($type);
        if($type == 'new_private_message'){
            if (!isset($sender)) $sender = 1;
            $author = '<a class="user-link"  href="'. get_author_posts_url($sender) .'" ><span class="avatar-notification">
                            ' . get_avatar($sender, 30) . '
                        </span>&nbsp;&nbsp;
                        <span class="date-notification">
                            ' . get_the_author_meta('display_name', $sender) . '
                        </span>
                        </a>';
            $message_link = '#';
            $message_link = '<a href="' . $message_link . '" >' . get_the_title($project) . '</a>';

            $content.= sprintf(__("%s sent you a private message %s", ET_DOMAIN) , $author, $message_link);
        }
        return $content;
    }
    /**
      * add more message notification menu on header
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_add_notification_menu(){
        ae_private_message_add_notification_menu_template();
    }
    /**
      * Get all reply of this conversation
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_get_replies_info(){
        $request = $_REQUEST;
        $response = array(
            'success'=> false,
            'msg'=> __('There are no reply in this conversation!1', ET_DOMAIN)
        );
        if( !isset($request['post_parent']) || $request['post_parent'] == ''){
            wp_send_json($response);
        }
        $args = array('post_parent'=> $request['post_parent']);
        $args = ae_private_message_replies_default_query_args($args);
        global $ae_post_factory, $post;
        $new_query = new WP_Query($args);
        $post_object = $ae_post_factory->get('ae_private_message');
        $replies_data = array();
        if( $new_query->have_posts() ) {
            while ($new_query->have_posts()) {
                $new_query->the_post();
                $convert = $post_object->convert($post);
                $replies_data[] = $convert;
            }
        }
        if( !empty($replies_data) ){
            $response = array(
                'success'=> true,
                'data'=> $replies_data,
                'msg'=> __('You got replies list success!', ET_DOMAIN)
            );
        }
        wp_send_json($response);
    }
    /**
      * Sync conversation
      * @param array $request
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_sync_conversation($request){
        global $ae_post_factory, $user_ID;
        $private_message = $ae_post_factory->get('ae_private_message');
        $response = array(
            'success'=> true,
            'data'=>$request
        );
        $success_msg = '';
        $is_create = false;
        if( isset($request['method']) && $request['method'] == 'create' ) {
            $response = $this->ae_private_message_validate($request);
            $request['archive_on_receiver'] = 0;
            $request['archive_on_sender'] = 0;
            $request['post_status'] = 'unread';
            $response['data'] = $request;
            $success_msg = __("Your message is sent successful!", ET_DOMAIN);
            $is_create = true;
        }
        if( $response['success'] ){
            $request = $response['data'];
        }
        else{
            wp_send_json($response);
        }
        if( isset($request['ID']) &&  $request['ID'] != '' ) {
            if ( !$this->ae_private_message_user_can_update_conversation($request)) {
                wp_send_json(array(
                    'success' => false,
                    'data' => '',
                    'msg' => 'You can not update this post'
                ));
            }
            $success_msg = __("Your message is updated successful!", ET_DOMAIN);
        }
        if(isset($request['archive']) && $request['archive'] == 1 ){
            $request = $this->ae_private_message_archive_message($request);
            $success_msg = __("Your message is archived successful!", ET_DOMAIN);
        }
        $result = $private_message->sync($request);
        if (!is_wp_error($result)) {
            //send email notification
            $user_email = ae_get_user_email( $result->to_user );
            if( $user_email && $is_create ) {
                $message = $this->ae_private_message_get_email_message($result);
                $author_name = get_the_author_meta('display_name', $result->from_user);
                $subject = sprintf(__('You have a new message from %s', ET_DOMAIN), $author_name );
                $this->ae_private_message_email_notification($user_email, $subject, $message, array('user_id'=>$result->to_user));
            }
            if( $is_create ){
                $this->ae_private_message_notification($result);
                ae_private_message_update_unread_reply($result);
//                $my_post = array(
//                    'ID'           => $result->ID,
//                    'post_status'   => 'publish'
//                );
//                wp_update_post( $my_post );
                //update latest user meta
                update_user_meta($user_ID, 'ae_private_message_latest', time());
                update_post_meta($result->bid_id, 'sent_private_msg', $result->ID);
            }
            else{
                if( isset($request['post_status']) && ( ae_user_role($user_ID) == EMPLOYER ||ae_user_role($user_ID) == 'administrator') ){
                    $my_post = array(
                        'ID'           => $result->ID,
                        'post_status'   => $request['post_status']
                    );
                    wp_update_post( $my_post );
                }
                $unread = ae_private_message_get_unread_reply($result);
                ae_private_message_update_new_number($user_ID, -$unread);
                ae_private_message_reset_unread_reply($result);
                $this->ae_private_message_update_latest_reply($result);
            }
            wp_send_json(array(
                'success' => true,
                'data' => $result,
                'msg' => $success_msg
            ));
        }
        else{
            wp_send_json(array(
                'success' => false,
                'data' => $result,
                'msg' => $result->get_error_message()
            ));
        }
    }
    /**
     * Sync reply
     * @param array $request
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_sync_reply($request){
        global $ae_post_factory, $user_ID;
        $private_message = $ae_post_factory->get('ae_private_message');
        $is_create = false;
        if( isset($request['method']) && $request['method'] == 'create' ) {
            $is_create = true;
        }
        if( $this->user_can_create_this_reply($request) && $is_create) {
            $response = $this->ae_private_message_check_time_delay('reply');
            if( !$response['success'] ){
                wp_send_json($response);
            }
            $request['is_conversation'] = 0;
            $request['post_status'] = 'publish';
            $result = $private_message->sync($request);
            if(!is_wp_error($result) ) {
                //$this->ae_private_message_notification($result);
                update_user_meta($user_ID, 'ae_private_message_reply_latest', time());
                $conversation =ae_private_message_get_conversation($result->post_parent);
                ae_private_message_update_unread_reply($conversation);
                if( ae_user_role($user_ID) == EMPLOYER ||ae_user_role($user_ID) == 'administrator') {
                    update_post_meta($result->post_parent, 'conversation_status', 'unread');
                    update_post_meta($result->post_parent, 'archive_on_receiver', 0);
                    update_post_meta($result->post_parent, 'employer_latest_reply', $result->post_content);
                    ae_private_message_update_new_number( $conversation->to_user );
                }
                else{
                    $mypost = array(
                        'ID' => $result->post_parent,
                        'post_status' => 'unread',
                    );
                    wp_update_post($mypost);
                    update_post_meta($result->post_parent, 'archive_on_sender', 0);
                    ae_private_message_update_new_number( $conversation->post_author );
                    update_post_meta($result->post_parent, 'freelancer_latest_reply', $result->post_content);
                }
                wp_send_json(array(
                    'success' => true,
                    'data' => $result,
                    'msg' => __("You created a reply successful!", ET_DOMAIN)
                    )
                );
            }
            else{
                wp_send_json(array(
                    'success' => false,
                    'data' => $result,
                    'msg' => $result->get_error_message()
                ));
            }
        }
        else if( $this->user_can_update_this_reply($request) ){
            if(isset($request['archive']) && $request['archive'] == 1 ){
               $request['post_status'] = 'archive';
                $result = $private_message->sync($request);
                if(!is_wp_error($result) ) {
                    wp_send_json(array(
                            'success' => true,
                            'data' => $result,
                            'msg' => __("You updated this reply successful!", ET_DOMAIN)
                        )
                    );
                }
                wp_send_json(array(
                    'success' => false,
                    'data' => $result,
                    'msg' => $result->get_error_message()
                ));
            }
            else{
                wp_send_json(array(
                    'success' => false,
                    'data'=> '',
                    'msg' => __("You can't update this reply!", ET_DOMAIN)
                ));
            }
        }
        else {
            wp_send_json(array(
                'success' => false,
                'data'=> '',
                'msg' => __("You can't sync reply!", ET_DOMAIN)
            ));
        }
    }
    /**
      * check user can sync this reply
      * @param array $request
      * @return bool true if user can create a reply or false if user can't
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function user_can_create_this_reply($request){
        global $user_ID, $ae_post_factory;
        if( !$user_ID ){
            return false;
        }
        if( !isset( $request['post_parent']) || $request['post_parent'] == ''){
            return false;
        }
        $post = get_post($request['post_parent']);
        if( !$post ){
            return false;
        }
        $post_obj = $ae_post_factory->get('ae_private_message');
        $conversation = $post_obj->convert($post);
        $response1 = $this->ae_private_message_authorize_sender($user_ID, $conversation->project_id, $conversation);
        $response2 = $this->ae_private_message_authorize_receiver($user_ID, $conversation->bid_id, $conversation);
        if( $response1['success'] || $response2['success'] ){
            return true;
        }
        return false;


    }
    /**
     * check user can update this reply
     * @param array $request
     * @return bool true if user can update this reply or false if can't
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function user_can_update_this_reply($request){
        global $user_ID;
        if( !$user_ID ){
            return false;
        }
        if( !isset($request['ID'] )) {
            return false;
        }
        $post = get_post($request['ID']);
        if( !$post ){
            return false;
        }
        if( $post->post_author == $user_ID && $post->post_type == 'ae_private_message'){
            return true;
        }
        return false;


    }
    /**
      * get number of unread message
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_get_unread(){
        global $user_ID;
        wp_send_json(
            array(
                'success'=>true,
                'data'=>ae_private_message_get_new_number($user_ID)
                )
        );
    }
    /**
      * post where clause
      * @param string $where
      * @param mixed $qry
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_posts_where($where, $qry){
        $post_parent = $qry->get('post_parent');
        if( $qry->get('post_type') == 'ae_private_message' && $post_parent != ''){
            global $wpdb;
            $add = $wpdb->prepare(" AND ( ( {$wpdb->posts}.post_parent = %s AND ( {$wpdb->postmeta}.meta_key = 'is_conversation' AND CAST( {$wpdb->postmeta}.meta_value AS CHAR) = %s) ) OR ( {$wpdb->posts}.ID = %s AND ( {$wpdb->postmeta}.meta_key = 'is_conversation' AND CAST( {$wpdb->postmeta}.meta_value AS CHAR) = %s)", $post_parent, 0, $post_parent, 1);
            $where = explode(" ", $where);
            $where = array_slice($where, 20);
            $where = implode(" ", $where);
            $where = $add.$where;
        }
        return $where;
    }
    /**
      * get latest reply of a conversation
      * @param object $conversation
      * @return string latest reply content of 0
      * @since 1.0
      * @package FREELANCEENGINE
      * @category PRIVATE MESSAGE
      * @author Tambh
      */
    public function ae_private_message_latest_reply($conversation){
        global $user_ID;
        if( ae_user_role($user_ID) == EMPLOYER ||ae_user_role($user_ID) == 'administrator') {
            return $conversation->freelancer_latest_reply;
        }
        else if(ae_user_role($user_ID) == FREELANCER){
            return $conversation->employer_latest_reply;
        }
        else{
            return false;
        }
    }
    /**
     * update latest reply of a conversation
     * @param object $conversation
     * @return object $conversation
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_update_latest_reply($conversation){
        global $user_ID;
        if( ae_user_role($user_ID) == EMPLOYER ||ae_user_role($user_ID) == 'administrator') {
            update_post_meta($conversation->ID, 'freelancer_latest_reply', false);
        }
        else if(ae_user_role($user_ID) == FREELANCER){
            update_post_meta($conversation->ID, 'employer_latest_reply', false);
        }
    }
    /**
     * update unread message
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category PRIVATE MESSAGE
     * @author Tambh
     */
    public function ae_private_message_update_unread(){
      global $user_ID, $ae_post_factory;
      $private_obj = $ae_post_factory->get('ae_private_message');
      $args = array();
      $args = ae_private_message_default_query_args($args);
      $args = wp_parse_args(array('showposts'=> -1), $args);
      $posts = query_posts($args);
      $role = ae_user_role($user_ID);
      $number = 0;
      if( !empty($posts) ){
        if( $role  == FREELANCER ){
          foreach ($posts as $key => $value) {
            $value = $private_obj->convert($value);
            if( $value->freelancer_unread > 0){
                $number += $value->freelancer_unread;
            }
          }
        }
        elseif ($role == EMPLOYER || $role == 'administrator') {
          foreach ($posts as $key => $value) {
            $value = $private_obj->convert($value);
            if( $value->employer_unread > 0){
              $number += $value->employer_unread;
            }
          }
        }
        update_user_meta($user_ID, 'fre_new_private_message', $number);
      }
      wp_reset_query();
    }
}