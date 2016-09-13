<?php
/**
 * Template Name: Recharge Pages
 */
global $user_ID;
$user_wallet = FRE_Credit_Users()->getUserWallet($user_ID);
?>
<div class="post-place-warpper" id="upgrade-account">
    <?php
    include dirname(__FILE__) . '/fre-credit-deposit-step1.php';
    if(!$user_ID) {
        include dirname(__FILE__) . '/fre-credit-deposit-step2.php';
    }
    include dirname(__FILE__) . '/fre-credit-deposit-step4.php';
    ?>
</div>
