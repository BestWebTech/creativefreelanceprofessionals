<?php
global $ae_post_factory, $post;
$his_obj = $ae_post_factory->get('fre_credit_history');
$convert = $his_obj->convert($post);
$style = $convert->style;
?>
<li class="history-item">
    <div class="information-detail">
        <p><i class="fa <?php echo $style['icon'] .' '.$style['color']?>"></i><?php echo $convert->post_title; ?> 
        	<span class="<?php echo $style['color'];?>"><?php echo $convert->amount_text ?></span> <?php echo $convert->info_changelog;?>
        </p>
        <p><?php _e('Balance:', ET_DOMAIN); ?> <span class="price"><?php echo $convert->user_balance; ?></span></p>
    </div>
    <div class="information-status">
        <p class="date"><?php echo $convert->history_time ?></p>
        <p class="status <?php echo $style['color'];?>"><?php echo $convert->history_status; ?><i class="fa fa-ellipsis-h"></i></p>
    </div>
</li>