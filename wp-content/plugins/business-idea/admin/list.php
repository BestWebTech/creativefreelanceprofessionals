<?php
include_once BUSINESS_PLUGIN_PATH."classes/settings.class.php";
$business_idea_setting = new business_idea_setting();

$page = (int) (!isset($_GET["spage"]) ? 1 : $_GET["spage"]);
$limit = 10;
$startpoint = ($page * $limit) - $limit;

$total_results = $business_idea_setting->getAllCount();
$aDatas = $business_idea_setting->getAll($startpoint, $limit);
?>
<div class="wrap">
	<h1>Business StartUps List</h1>
    <div class="pagi">
	<?php 
    $url = admin_url('/admin.php?page=business-startup-welcome');
    echo $business_idea_setting->pagination($limit,$page,$url,$total_results);
    ?>  
    </div>
	<div class="Business_Data">
    	<table cellspacing="0" border="0" cellspadding="0" class="data_idea_table wp-list-table widefat fixed striped posts">
        	<thead>
            	<tr>
                    <th class="manage-column" width="10%"><strong><span>S No.</span></strong></th>
                    <th class="manage-column" width="15%"><strong><span>Name</span></strong></th>
                    <th class="manage-column" width="15%"><strong><span>Start Up Name</span></strong></th>
                    <th class="manage-column" width="10%"><strong><span>Budget</span></strong></th>
                    <th class="manage-column" width="15%"><strong><span>Start Up Submited</span></strong></th>
                    <th class="manage-column" width="10%"></th>
                </tr>
            </thead>
            <tbody>
            <?php if(count($aDatas)>0){ ?>
			<?php foreach($aDatas as $aData){ ?>
				<tr>
                    <td><span><?php echo $aData->id ?></span></td>
                    <td><span><?php echo $aData->full_name ?></span></td>
                    <td><span><?php echo $aData->start_up_name ?></span></td>
                    <td><span>&#36;<?php echo $aData->budget ?></span></td>
                    <td><span><?php echo date("d-F-Y",$aData->created); ?></span></td>
                    <td><span><a href="<?php echo admin_url('/admin.php?page=business-startup-view&id='.$aData->id) ?>" class="button">View StartUp</a></span></td>
                    
                </tr>                
            <?php } ?>
            <?php }else{ ?>
            <tr>
            	<td colspan="9"> No Entry Found!</td>
            </tr>
            <?php } ?>
        	</tbody>
    	</table>
    </div>
    <div class="pagi">
	<?php 
    echo $business_idea_setting->pagination($limit,$page,$url,$total_results);
    ?>  
    </div>
	<br />
	<br />
	<h2>Use Short Codes</h2>
	
	<ul>
	  <li>For display payment form use shortcode <b> [wp_business_idea_form]</b></li>
		<!--<li>For display response use shortcode <b>[wp_fssnet_payment_status]</b></li>-->
	</ul>
	
</div>
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
.pagi {
    display: inline-block;
    margin-top: 10px;
    width: 100%;
}
.pagi .details {
	 float: right;
}

.pagi .pagination {
    float: left;
    margin: 0;
}
.pagination li {
    display: inline-block;
    padding: 0 5px;
}
.pagination li a {
	background: rgb(228, 228, 228) none repeat scroll 0 0;
	border: 1px solid rgb(210, 210, 210);
	display: inline-block;
	font-size: 16px;
	font-weight: 400;
	line-height: 1;
	min-width: 17px;
	padding: 3px 5px 7px;
	text-align: center;	
	text-decoration:none;
}
.pagination li a:hover{
	background: rgb(0, 160, 210) none repeat scroll 0 0;
    border-color: rgb(91, 157, 217);
    box-shadow: none;
    color: rgb(255, 255, 255);
    outline: 0 none;
}
.pagination li span.current {
    background: rgb(235, 235, 235) none repeat scroll 0 0;
    border: 1px solid rgb(210, 210, 210);
    display: inline-block;
    font-size: 16px;
    font-weight: 400;
    line-height: 1;
    min-width: 17px;
    padding: 3px 5px 7px;
    text-align: center;
	height: 16px;
}
</style>
