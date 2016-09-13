<?php

include_once BUSINESS_PLUGIN_PATH."classes/settings.class.php";
$business_idea_setting = new business_idea_setting();
if($_REQUEST['id']){
	$id = $_REQUEST['id']; 
	if($startUp = $business_idea_setting->getOne($id)){
?>
<div class="wrap">
	<h1>Business StartUp Detail</h1><br />
     <a href="<?php echo admin_url('/admin.php?page=business-startup-welcome') ?>" class="button">Back StartUp List</a>
     <br /><br />
	<div class="Business_Data">
    	<table cellspacing="0" border="0" cellspadding="0" class="data_idea_table wp-list-table widefat fixed striped posts">
        <tbody>
            <tr>
                <td width="20%"><strong><span>Name</span></strong></td>
                <td width="80%"><span><?php echo $startUp->full_name ?></span></td>
            </tr>
            <tr>
            	<td><strong><span>Email</span></strong></td>
                <td><span><a href="mailto:<?php echo $startUp->user_email ?>"><?php echo $startUp->user_email ?></a></span></td>
            </tr>
            <tr>
            	<td><strong><span>StartUps Name</span></strong></td>
                <td><span><?php echo $startUp->start_up_name ?></span></td>
            </tr>
            <tr>
            	<td><strong><span>StartUps Idea</span></strong></td>
                <td><div class="start_idea"><?php echo $startUp->idea ?></div></td>
            </tr>
            <tr>
            	<td><strong><span>Budget</span></strong></td>
                <td><span>&#36;<?php echo $startUp->budget ?></span></td>
            </tr>
            <tr>
            	<td><strong><span>Start Up Submited</span></strong></td>
                <td><span><?php echo date("d-F-Y",$startUp->created); ?></span></td>
            </tr>
            <tr>
                <td><strong><span>StartUps videos</span></strong></td>
                <td>
                <?php if(!empty($startUp->youtube_video) || !empty($startUp->vimeo_video)){  ?>
                    <?php if(!empty($startUp->youtube_video)){ ?>
                        <?php parse_str( parse_url($startUp->youtube_video, PHP_URL_QUERY ), $youtube_url_code ); ?>
                        <!--<iframe width="560" height="315" src="https://www.youtube.com/embed/FKksMQEIOH8" frameborder="0" allowfullscreen></iframe>-->
                        <iframe width="300" height="150" src="http://www.youtube.com/embed/<?php echo $youtube_url_code['v']; ?>" frameborder="0" allowfullscreen></iframe>
                    <?php } ?>
                    <?php 
                    if(!empty($startUp->vimeo_video)){
                        $videoLink = 'https://vimeo.com/channels/staffpicks/142216434';
                        if (preg_match("/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $videoLink, $id)) {
                         $videoId = $id[3];
                        }
                    ?>
                        <iframe src="//player.vimeo.com/video/<?php echo $videoId ?>" width="WIDTH" height="HEIGHT" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    <?php } ?>
                <?php }else{ ?>
                    No StartUps Video!
                <?php } ?>
                </td>
            </tr>
            <tr>
                <td><strong><span>Attached Document</span></strong></td>
                <td>
                    <span>
                        <?php $aDocments = explode(',',$startUp->attachment);$i = 1; ?>
                        <?php $lenth = count($aDocments) ?>
                        <?php foreach($aDocments as $aDocment){?>
                        	<a target="_blank" href="<?php echo $aDocment ?>">Download Attachment<?php echo $i; ?></a>
                            <?php if ($i != $lenth) { ?>
                            &nbsp;|&nbsp;
                            <?php };$i++; ?>
                        <?php } ?>
                    </span>
                </td>
            </tr>
        </tbody>
    	</table>
    </div>
    <br />
    <a href="<?php echo admin_url('/admin.php?page=business-startup-welcome') ?>" class="button">Back StartUp List</a>
	<br />
	<br />
	<h2>Use Short Codes</h2>
	
	<ul>
	  <li>For display payment form use shortcode <b> [wp_business_idea_form]</b></li>
		<!--<li>For display response use shortcode <b>[wp_fssnet_payment_status]</b></li>-->
	</ul>
	
</div>
<?php
	}
}else{
	wp_redirect(admin_url('/admin.php?page=business-startup-welcome', 'http'), 301);	
}
?>
<style type="text/css">
	.Business_Data .data_idea_table{
		width:100%;	
	}
	

.data_idea_table {
    background: rgb(255, 255, 255) none repeat scroll 0 0;
    border: 1px solid rgb(229, 229, 229);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
}
.data_idea_table thead th {
    border-bottom: 1px solid rgb(225, 225, 225);
    color: rgb(51, 51, 51);
    text-align: left;
}
table.data_idea_table thead td {
    font-weight: 600;

}
table.data_idea_table tbody td {
    border-bottom: 1px solid rgb(225, 225, 225);
}
.error-alert {
    background: rgb(250, 228, 228) none repeat scroll 0 0;
    border: 1px solid rgb(237, 140, 140);
    border-radius: 5px;
    color: rgb(202, 105, 105);
    margin-bottom: 15px;
    padding: 10px;
	margin:20px 0;
	width:96%;
}
.success-alert {
    background: rgb(238, 247, 234) none repeat scroll 0 0;
    border: 1px solid rgb(187, 219, 161);
    border-radius: 5px;
    color: rgb(136, 168, 110);
    margin-bottom: 15px;
    padding: 10px;
	margin:20px 0;
	width:96%;
}
.start_idea {
    height: 300px;
    overflow: auto;
}
</style>
