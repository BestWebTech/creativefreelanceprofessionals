<?php
/**
 * Plugin  function
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
*/
/**
  * get user profile
  * @param int $user_id the ID of user
  * @return array $userdata
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
if( !function_exists('ae_private_msg_user_profile') ){
	function ae_private_msg_user_profile( $user_id ){
		global $ae_post_factory;
		$profile_object = $ae_post_factory->get(PROFILE);
		$profile_id = get_user_meta($user_id, 'user_profile_id', true);
		$profile = get_post($profile_id);		
		$profile = $profile_object->convert($profile);		
		$userdata = array(
			'ID'=> $user_id,
			'position'=> $profile->et_professional_title,
			'avatar'=> get_avatar($user_id, 70),
			'user_name'=> $profile->post_title,
			'user_link'=> $profile->guid
		);
		return $userdata;
	}
}
/**
  * Check user is a project's owner
  * @param integer $user_id
  * @param integer $project_id
  * @return bool true/ false
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
function is_project_owner( $user_id, $project_id ){
	$project = get_post( $project_id );
	if( isset($project->post_author) && (int)$project->post_author == $user_id ){
		return true;
	}
	return false;

}
/**
 * Check user is a project's owner
 * @param integer $user_id
 * @param integer $bid_id
 * @return bool true/ false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function is_bid_owner( $user_id, $bid_id ){
	$bid = get_post( $bid_id );
	if( isset($bid->post_author) && (int)$bid->post_author == $user_id ){
		return true;
	}
	return false;

}
/**
 * Get user email
 * @param integer $user_id
 * @return string $email | false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_get_user_email( $user_id ){
	$user_data = get_userdata( $user_id );
	if( isset($user_data->user_email) ) {
		$user_email = $user_data->user_email;
		return $user_email;
	}
	else{
		return false;
	}
}
/**
 * Get user display name
 * @param integer $user_id
 * @return string $display_name | false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_get_user_display_name( $user_id ){
	$user_data = get_userdata( $user_id );
	if( isset($user_data->display_name ) ) {
		$display_name  = $user_data->display_name ;
		return $display_name ;
	}
	else{
		return false;
	}
}
/**
  * default query_args of private message fetch
  * @param array $args
  * @return array $args after filter
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
function ae_private_message_default_query_args( $args ){
	global $user_ID;
	$default = array(
		'post_type' => 'ae_private_message',
		'post_status' => array('publish', 'unread'),
		'meta_query' => array(
			array(
				'key' => 'is_conversation',
				'value' => '1'
			)
		)
	);
	$args = wp_parse_args( $args, $default);
	if( ae_user_role($user_ID) == EMPLOYER ||  ae_user_role($user_ID) == 'administrator'){
		$args = wp_parse_args( array('author'=> $user_ID), $args);
		$meta_query = array(
			'relation'=> 'AND',
			array(
				'key'=>'archive_on_sender',
				'value'=> 0
			)
		);
		$args['meta_query'] = wp_parse_args( $meta_query, $args['meta_query']);
	}
	else if(ae_user_role($user_ID) == FREELANCER ) {
		$meta_query = array(
			'relation' => 'AND',
			array(
				'key' => 'to_user',
				'value' => $user_ID
			),
			array(
				'key' => 'archive_on_receiver',
				'value' => 0
			)
		);
		$args['meta_query'] = wp_parse_args($meta_query, $args['meta_query']);
	}
	return $args;
}
/**
 * default query_args of private message replies fetch
 * @param array $args
 * @return array $args after filter
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_replies_default_query_args( $args ){
	$default = array(
		'post_type' => 'ae_private_message',
		'post_status' => array('publish', 'unread'),
		'meta_query' => array(
			array(
				'key' => 'is_conversation',
				'value' => '0'
			)
		)
	);
	$args = wp_parse_args( $args, $default);
	return $args;
}
/**
 * get new message number
 * @param int $user_id the ID of user
 * @return integer  new message number
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_get_new_number( $user_id ){
	return get_user_meta($user_id, 'fre_new_private_message', true);
}
/**
 * update new message
 * @param integer $user_id
 * @param integer $pl
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_update_new_number( $user_id, $pl = 1 ){
	$number = ae_private_message_get_new_number($user_id);
	$number += $pl;
	if( $number < 0 ){
		$number = 0;
	}
	update_user_meta($user_id, 'fre_new_private_message', $number);
}
/**
 * update new message
 * @param int $user_id
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_reset_unread_message( $user_id ){
	update_user_meta($user_id, 'fre_new_private_message', 0);
}
/**
 * get conversation info
 * @param int $conversation_id
 * @return object conversation or false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_get_conversation( $conversation_id )
{
	global $ae_post_factory;
	$post = get_post($conversation_id);
	$conversation = false;
	if ($post) {
		$post_obj = $ae_post_factory->get('ae_private_message');
		$conversation = $post_obj->convert($post);
	}
	return $conversation;
}
/**
  * update freelancer unread reply
  * @param object $conversation
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
 function ae_private_message_update_unread_reply( $conversation ){
	 global $user_ID;
	 if( ae_user_role($user_ID) == EMPLOYER || ae_user_role($user_ID) == 'administrator'){
		 $unread = 0;
		 if( isset($conversation->freelancer_unread) ) {
			 $unread = (int)$conversation->freelancer_unread;
		 }
		 $unread += 1;
		 update_post_meta($conversation->ID, 'freelancer_unread', $unread);
	 }
	 else if(ae_user_role($user_ID) == FREELANCER ){
		 $unread = 0;
		 if( isset($conversation->employer_unread)){
			 $unread = (int)$conversation->employer_unread;
		 }
		 $unread += 1;
		 update_post_meta($conversation->id, 'employer_unread', $unread);
	 }
 }
/**
 * reset freelancer unread reply
 * @param object $conversation
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_reset_unread_reply( $conversation ){
	global $user_ID;
	if( ae_user_role($user_ID) == EMPLOYER || ae_user_role($user_ID) == 'administrator'){
		update_post_meta($conversation->ID, 'employer_unread', 0);
	}
	else if(ae_user_role($user_ID) == FREELANCER ){
		update_post_meta($conversation->id, 'freelancer_unread', 0);
	}
}
/**
 * get freelancer or employer unread reply
 * @param object $conversation
 * @return integer unread reply
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_get_unread_reply( $conversation )
{
	global $user_ID;
	if(isset($conversation->employer_unread) ) {
		if (ae_user_role($user_ID) == EMPLOYER || ae_user_role($user_ID) == 'administrator') {
			return (int)$conversation->employer_unread;
		} else if (ae_user_role($user_ID) == FREELANCER) {
			return (int)$conversation->freelancer_unread;
		}
	}
	return 1;
}
/**
 * check did employer send a conversation to a freelancer
 * @param array $data
 * @return array $response
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_created_a_conversation($data){
	$response = array(
		'success' => false,
		'msg' => __("You already created a conversation for this bid!", ET_DOMAIN)
	);
	if( !isset($data['bid_id']) ){
		return $response;
	}
	$sent = get_post_meta($data['bid_id'], 'sent_private_msg', true);
	$response['conversation_id'] = $sent;
	if( !$sent ){
		$response = array(
			'success' => true,
			'msg' => __("You hasn't created a conversation for this bid,yet!", ET_DOMAIN),
			'data'=> $data
		);
		return $response;
	}
	return $response;

}
/**
  * check current user can view this conversation
  * @param array $conversation
  * @return bool true if current user can view this conversation and false if can't
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
function ae_private_message_user_can_view_conversation($conversation){
	global $user_ID;
	if( $user_ID == $conversation->post_author || $user_ID == $conversation->to_user){
		return true;
	}
	return false;
}
