<?php
/*======================================================================*\
|| #################################################################### ||
|| #             This file is part of Account Locker Lite             # ||
|| # ---------------------------------------------------------------- # ||
|| #         Copyright © 2014 Gewora.net. All Rights Reserved.        # ||
|| # This file may not be redistributed in whole or significant part  # ||
|| #                                                                  # ||
|| #            For more license information contact Gewora           # ||
|| #            and/or read the license details which have            # ||
|| #                  been included in this product                   # ||
|| #                                                                  # ||
|| # You are NOT allowed to modify/remove this copyright information  # ||
|| #                                                                  # ||
|| #              Any infringement of this copyright will             # ||
|| #               result in legal action by the holder               # ||
|| #                                                                  # ||
|| # -------------------- http://www.gewora.net --------------------- # ||
|| #################################################################### ||
\*======================================================================*/
?>

<h3>Lock User Account</h3>
<table class="form-table">
    <tr>
        <th><label for="account_status">Account status</label></th>
        <td>
            <select name="account_status" id="account_status">
                <option value="unlocked" <?php Selected(!$locking_data) ?> >Unlocked</option>
                <option value="locked" <?php Selected($locking_data) ?> >Locked</option>
            </select>
        </td>
    </tr>
</table>