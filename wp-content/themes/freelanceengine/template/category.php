<?php
/**
 * Template Name: Category Page
*/
?>

<?php get_header(); ?>




<?php


global $wpdb;
$categories = $wpdb->get_results("SELECT zqpr_term_taxonomy.term_id, zqpr_term_taxonomy.taxonomy, zqpr_terms.name, zqpr_terms.slug FROM zqpr_term_taxonomy INNER JOIN zqpr_terms ON zqpr_term_taxonomy.term_id=zqpr_terms.term_id WHERE zqpr_term_taxonomy.taxonomy = 'project_category'");

   foreach ($categories as $categoryData)
	{
        $categoryData =  (array)$categoryData; ?>
        <div class="project-groups">
		<h2>
			<a target="_blank" href="<?php echo site_url().'/project_category/'.$categoryData['slug']; ?>" title="Click here to view all the project of <?php echo $categoryData['name']; ?>">
				<?php echo $categoryData['name']; ?>
			</a>
            [ <?php $args = array('project_category' => $categoryData['slug'],'post_type' => 'project');
                     $posts_array = get_posts( $args );
                     echo count($posts_array);
                     ?> ]
		</h2>
        </div>
        <?php }	 ?>
        
        

<?php get_footer(); ?>


