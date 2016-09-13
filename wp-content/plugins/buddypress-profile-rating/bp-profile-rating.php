<?php
/**
 * @package Akismet
 */
/*
Plugin Name: Buddypress Profile Rating
Plugin URI: http://souravonline.com/
Description: This Buddypress Profile Rating plugin used for Buddypress users to rate each others profile and can see the average rate of someones profile .
Version: 1.0
Author: Sourav Sarkar
Author URI: http://souravonline.com/
License: GPLv2 or later
Text Domain: souravonline.com
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly

add_action( 'bp_profile_header_meta', 'bp_user_rate' );
function bp_user_rate() {
    
   
 
   
	if($_REQUEST['bp_rate']!='')
	{
		$bp_user_rate_uid=get_option('bp_user_rate');
		if($bp_user_rate_uid!='')
		{
			
				
				
				$bp_user_rate_uid[bp_displayed_user_id()][get_current_user_id()][0]=$_REQUEST['bp_rate'];
				update_option('bp_user_rate',$bp_user_rate_uid);
			echo "<br><br>";
			
			
		}else{
				
				$bp_user_rate_uid[bp_displayed_user_id()][get_current_user_id()][0]=$_REQUEST['bp_rate'];
				add_option('bp_user_rate',$bp_user_rate_uid);
		}
		
	}	
		
	$bp_user_rate_uid=get_option('bp_user_rate');
	$total_rate=0;
	
	if(!empty($bp_user_rate_uid[bp_displayed_user_id()]))
	{
		$bp_user_rate_array = array_values($bp_user_rate_uid[bp_displayed_user_id()]);
		
		for($i=0;$i<=count($bp_user_rate_array);$i++)
			{
				$total_rate=$total_rate+$bp_user_rate_array[$i][0];
			}
	}else{
		$total_rate=0;
	}
	?>
    <div class="bp_rating">
    <span class="avrate"><?php _e('Average rating','bp-profile-rating'); ?> : </span>
    <?php	
		
	if($total_rate>0){
		$avg_rate=	$total_rate/count($bp_user_rate_uid[bp_displayed_user_id()]);
		
	}else{
	$avg_rate=0;
	}	
		for ($i = 1; $i <= floor($avg_rate); $i++) { 
		?>
        <span  class="yellow_star" alt="<?php echo $avg_rate; ?>" title="<?php echo $avg_rate; ?>">&nbsp;</span>
        <?php
		}
		if (is_float($avg_rate)) { 
		$fl=1;
		?>        
		<span   class="half_star" alt="<?php echo $avg_rate; ?>" title="<?php echo $avg_rate; ?>">&nbsp;</span>
        <?php	
		}
		$blnk=5-floor($avg_rate)-$fl;
		for ($i = 1; $i <=$blnk ; $i++) { 
		?>
        <span  class="blank_star" alt="<?php echo $avg_rate; ?>" title="<?php echo $avg_rate; ?>">&nbsp;</span>
        <?php
		}
		
	?>
    </div>
    <?php
	if(bp_displayed_user_id()!=get_current_user_id() && get_current_user_id()!=0){
	$bp_user_rate_uid=get_option('bp_user_rate');
	$urate=$bp_user_rate_uid[bp_displayed_user_id()][get_current_user_id()][0];
	if($urate=='')
	{
	$urate=0;
	}
	?>
	
	
	
    <div class="bp_rating">
    <span class="avrate"><?php _e('Your raing','bp-profile-rating'); ?> : </span>
    <?php 
		for($i=1;$i<=$urate;$i++)
		{ 
	?>
    <a href="?bp_rate=<?php echo $i; ?>" id="bp_rate_<?php echo $i; ?>" class="yellow_star" onmouseover="bp_rate(<?php echo $i; ?>)" onmouseout="bp_rate_rev(<?php echo $i.",".$urate; ?>)">&nbsp; </a> 
    <?php 
		} 
		if($urate!=5){
			for($i=$urate+1;$i<=5;$i++)
			{
	?>
     <a href="?bp_rate=<?php echo $i; ?>" id="bp_rate_<?php echo $i; ?>" class="blank_star" onmouseover="bp_rate(<?php echo $i; ?>)" onmouseout="bp_rate_rev(<?php echo $i.",".$urate; ?>)">&nbsp; </a>
    <?php		
			}		
		}
	?>   
    </div>
<?php 
	}
     
}

function bp_user_rate_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'style', $plugin_url . 'css/style.css' );
	wp_enqueue_script( 'script-name', $plugin_url. 'bp-rate.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'bp_user_rate_css' );
