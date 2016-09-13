<?php
class business_idea_setting {
	
	public function add($aVals){
		global $wpdb;	
		if($aVals)
		{
			$aVals['status'] = 0;
			$aVals['created'] = time();
			$wpdb->insert($wpdb->base_prefix.'business_idea', $aVals);	
		}		
		if($wpdb->insert_id != 0){
			return true;	
		}else{
			return false;	
		}
		
	}
	public function update($aVals) {
		global $wpdb;
		if($aVals)
		{
			$wpdb->update( 
						$wpdb->base_prefix.'business_idea', 
						array( 
							'status' => $aVals['status']
						), 
						array( 'id' => $aVals['id'] ), 
						array( 
							'%d'
						), 
						array( '%d' ) 
			);
		//$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->base_prefix."business_idea SET status=%d WHERE id='$id'",$aVals['status']));
		}		
		return true;	
	}
	
					
	public function getAll($start,$limit)
	{
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->base_prefix."business_idea  LIMIT ".$start.",".$limit;
		$aRows = $wpdb->get_results($sql);
		return $aRows;
	}
	
	public function getAllCount()
	{
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM ".$wpdb->base_prefix."business_idea";
		$aRows = $wpdb->get_var($sql);
		return $aRows;
	}
	
	public function getOne($id)
	{
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->base_prefix."business_idea WHERE id =".$id;
		$aRows = $wpdb->get_row($sql);
		return $aRows;
	}
	
	public function pagination($per_page,$page = 1, $url,$total){        
        $adjacents = "2"; 

    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;								
		
    	$prev = $page - 1;							
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;
    	
    	$pagination = "";
		 
    	if($lastpage > 1)
    	{	
    		$pagination .= "<ul class='pagination'>";
                   
    		if ($lastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li><span class='current'>$counter</span></li>";
    				else
    					$pagination.= "<li><a href='{$url}&spage=$counter'>$counter</a></li>";					
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><span class='current'>$counter</span></li>";
    					else
    						$pagination.= "<li><a href='{$url}&spage=$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='dot'>...</li>";
    				$pagination.= "<li><a href='{$url}&spage=$lpm1'>$lpm1</a></li>";
    				$pagination.= "<li><a href='{$url}&spage=$lastpage'>$lastpage</a></li>";		
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='{$url}&spage=1'>1</a></li>";
    				$pagination.= "<li><a href='{$url}&spage=2'>2</a></li>";
    				$pagination.= "<li class='dot'>...</li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='{$url}&spage=$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='dot'>..</li>";
    				$pagination.= "<li><a href='{$url}&spage=$lpm1'>$lpm1</a></li>";
    				$pagination.= "<li><a href='{$url}&spage=$lastpage'>$lastpage</a></li>";		
    			}
    			else
    			{
    				$pagination.= "<li><a href='{$url}&spage=1'>1</a></li>";
    				$pagination.= "<li><a href='{$url}$spage=2'>2</a></li>";
    				$pagination.= "<li class='dot'>..</li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><span class='current'>$counter</span></li>";
    					else
    						$pagination.= "<li><a href='{$url}&spage=$counter'>$counter</a></li>";					
    				}
    			}
    		}
    		/*
    		if ($page < $counter - 1){ 
    			$pagination.= "<li><a href='{$url}&spage=$next'>Next</a></li>";
                $pagination.= "<li><a href='{$url}&spage=$lastpage'>Last</a></li>";
    		}else{
    			$pagination.= "<li><a class='current'>Next</a></li>";
                $pagination.= "<li><a class='current'>Last</a></li>";
            }*/	
    		$pagination.= "</ul>\n";		
    	}
    	$pagination .= "<div class='details'><strong>Page $page of $lastpage</strong></div>";
    
        return $pagination;
    } 

	
	

}

?>
