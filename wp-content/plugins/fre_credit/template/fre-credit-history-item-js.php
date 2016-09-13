<script type="text/template" id="fre-credit-history-loop">

    <div class="information-detail">
        <p>
	        <i class="fa {{= style.icon}} {{= style.color}}"></i>{{= post_title }} 
	        <span class="{{= style.color}}">{{= amount_text }}</span> {{= info_changelog}}
        </p>
        <p><?php _e('Balance:', ET_DOMAIN); ?> <span class="price">{{= user_balance }}</span></p>
    </div>
    <div class="information-status">
        <p class="date">{{= history_time }}</p>
        <p class="status {{= style.color}}">{{= history_status }}<i class="fa fa-ellipsis-h"></i></p>
    </div>

</script>