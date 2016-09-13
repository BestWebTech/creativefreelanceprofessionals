<?php
class FRE_Credit_History extends AE_Posts
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
     * @param string $post_type
     * @param array $taxs
     * @param array $meta_data
     * @param array $localize
     * @return void void
     *
     * @since 1.0
     * @author Jack Bui
     */
    public function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array())
    {
        parent::__construct('fre_credit_history', $taxs, $meta_data, $localize);
    }
    /**
     * init for this class
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function init()
    {
        $this->fre_credit_register_post_type();
    }
    /**
     * register post type
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function  fre_credit_register_post_type(){

        register_post_type('fre_credit_history', array(
            'labels' => array(
                'name' => __('Credit history', ET_DOMAIN) ,
                'singular_name' => __('Credit history', ET_DOMAIN) ,
                'add_new' => __('Add New', ET_DOMAIN) ,
                'add_new_item' => __('Add New Credit history', ET_DOMAIN) ,
                'edit_item' => __('Edit Credit history', ET_DOMAIN) ,
                'new_item' => __('New Credit history', ET_DOMAIN) ,
                'all_items' => __('All Credit histories', ET_DOMAIN) ,
                'view_item' => __('View Credit history', ET_DOMAIN) ,
                'search_items' => __('Search Credit histories', ET_DOMAIN) ,
                'not_found' => __('No Credit history found', ET_DOMAIN) ,
                'not_found_in_trash' => __('No Credit histories found in Trash', ET_DOMAIN) ,
                'parent_item_colon' => '',
                'menu_name' => __('Credit histories', ET_DOMAIN)
            ) ,
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => true,

            'capability_type' => 'post',
            // 'capabilities' => array(
            //     'manage_options'
            // ) ,
            'has_archive' => 'packs',
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array(
                'title',
                'editor',
                'author',
                'custom-fields'
            )
        ));
        global $ae_post_factory;
        $tax = array();
        $meta = array(
            'history_type',
            'history_status',
            'amount',
            'currency',
            'history_time',
            'user_balance'
        );
        $ae_post_factory->set('fre_credit_history', new AE_Posts('fre_credit_history', $tax, $meta));
    }
    /**
      * save History
      *
      * @param array $args
      * @return integer $history_id
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function saveHistory($args){
        global $user_ID;
        $history_post = array(
            'post_type' => 'fre_credit_history',
            'post_status' => 'publish',
            'post_author' => $user_ID,
            'post_title' => __('credit history', ET_DOMAIN),
            'post_content' => 'charge ' . $args['amount']
        );
        $default = array(
            "destination" => '',
            "source_transaction" => '',
            "statement_descriptor" => __("Freelance escrow", ET_DOMAIN)
        );
        $args = wp_parse_args($args, $default);
        if( isset( $args['post_title'] ) ){
            $history_post['post_title']= $args['post_title'];
        }
        
        $history_id = wp_insert_post($history_post);
        update_post_meta($history_id, 'history_type', $args['history_type']);
        update_post_meta($history_id, 'history_status', $args['status']);
        update_post_meta($history_id, 'amount', $args['amount']);
        update_post_meta($history_id, 'currency', $args['currency']);
        update_post_meta($history_id, 'destination', $args['destination']);
        update_post_meta($history_id, 'source_transaction', $args['source_transaction']);
        update_post_meta($history_id, 'statement_descriptor', $args['statement_descriptor']);
        
        if(isset($args['payment'])){
            update_post_meta($history_id, 'payment', $args['payment']);
        }
        update_post_meta($history_id, 'user_balance', fre_price_format(FRE_Credit_Users()->getUserWallet($user_ID)->balance));
        return $history_id;
    }
    /**
      * retrieve charge information
      *
      * @param integer $id
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function retrieveHistory($id){
        global $ae_post_factory;
        $history_obj = $ae_post_factory->get('fre_credit_history');
        $post = get_post($id);
        $history = $history_obj->convert($post);
        return $history;
    }
}
class Fre_Credit_HistoryAction extends AE_PostAction
{
    function __construct($post_type = 'fre_credit_history')
    {
        $this->post_type = 'fre_credit_history';
        // add action fetch profile
        $this->add_filter('ae_convert_fre_credit_history', 'fre_credit_convert_history');
        $this->add_ajax('fre-fetch-history', 'fetch_post');
    }
    /**
      * convert history object
      *
      * @param object $result
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public  function fre_credit_convert_history($result){
        global $user_ID;
        $post_date = get_post_time('Y-m-d h:i:s', true, $result->ID);
        $result->history_time = sprintf( _x( '%s ago', '%s = human-readable time difference', ET_DOMAIN ), human_time_diff( strtotime($post_date), time() ));
        $result->amount_text = fre_price_format($result->amount);
        $result->style = get_color_icon_transaction($result->history_type);
        $payment = get_post_meta($result->ID, 'payment', true);
        $payment_method = '';
        
        $result->info_changelog = '';
        if( $result->post_title == 'withdrew' ){
            if( $payment == 'email-paypal-credit' ){
                $payment_method = get_user_meta($user_ID, $payment, true);
                $result->info_changelog = sprintf(__('to Paypal <u>%s</u>', ET_DOMAIN), $payment_method);
            }elseif( $payment == 'bank-info-credit' ){
                $payment_method = get_user_meta($user_ID, $payment, true);
                $result->info_changelog = sprintf(__('to Bank <u>%s</u>', ET_DOMAIN), 
                                                    $payment_method['benficial_owner'], 
                                                    $payment_method['account_number']);
            }
        }elseif( $result->post_title == 'Deposited' ){
            if( !empty($payment) )
                $result->info_changelog = sprintf(__('via %s', ET_DOMAIN), $payment);

        }elseif( $result->post_title == 'Paid' && !empty($payment)){
            $project = get_post($payment);
            $result->info_changelog = sprintf( __('for %s project <u>%s</u> ', ET_DOMAIN), 
                                          $project->post_status, 
                                          $project->post_title);
            
        }elseif( $result->post_title == 'Received' && !empty($payment)){
            $project = get_post($payment);
              
            $author = get_user_by( 'id', $project->post_author); 
            $result->info_changelog = sprintf( __('from %s - Project <u>%s</u> ', ET_DOMAIN), 
                                          $author->display_name, 
                                          $project->post_title);
        }
        
        return $result;
    }
    /**
     * filter query
     *
     * @param array $query_args
     * @return array $query_args after filter
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function filter_query_args($query_args){
        if( isset($_REQUEST['query']['history_type']) && !empty($_REQUEST['query']['history_type']) ){
            $query_args['meta_query'] = array(
                array(
                'key'=> 'history_type',
                'value'=> $_REQUEST['query']['history_type'] )
                );
        }
        $date_format = get_option( 'date_format' ).' '. get_option('time_format');
        $from = date($date_format);
        $to = date($date_format);
        $flag = false;
        if( isset($_REQUEST['query']['fre_credit_from']) && !empty($_REQUEST['query']['fre_credit_from']) ){
            $str_from = $_REQUEST['query']['fre_credit_from'] . ' 00:00';
            $from = date($date_format, strtotime($str_from) );
            $flag = true;
        }
        if( isset($_REQUEST['query']['fre_credit_to']) && !empty($_REQUEST['query']['fre_credit_to']) ){
            $str_to = $_REQUEST['query']['fre_credit_to'] . ' 23:59';
            $to = date($date_format, strtotime($str_to) );
            $flag = true;
        }
        if( $flag && $from != $to) {
            $query_args['date_query'] = array(
                'after' => $from,
                'before' => $to,
                'inclusive' => true
            );
        }
        return $query_args;
    }
}
new Fre_Credit_HistoryAction();