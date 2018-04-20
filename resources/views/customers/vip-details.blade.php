<form action="" name="modal-edit-vip-form" method="post">
    <div class="alert hidden"></div>
    <input type="hidden" name="id" value="<?php echo $customer->id; ?>" />
    <table class="table table-bordered">
        <tr>
            <td>Id</td>
            <td><?php echo $customer->id; ?></td>
        </tr>
        <tr>
            <td>Firstname</td>
            <td><input name="first_name" class="form-control" type="text" value="<?php echo $customer->first_name; ?>" /></td>
        </tr>
        <tr>
            <td>Lastname</td>
            <td><input name="last_name" class="form-control" type="text" value="<?php echo $customer->last_name; ?>" /></td>
        </tr>
        <tr>
            <td>From</td>
            <td>
                <select name="customer_from" class="form-control">
                    <option value="">Other</option>
                    <option value="facebook" <?php echo ($customer->customer_from === 'facebook' ? ' selected ' : ''); ?>>facebook</option>
                    <option value="telegram" <?php echo ($customer->customer_from === 'telegram' ? ' selected ' : ''); ?>>telegram</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Amo contact</td>
            <td><input name="amocrm_contact_id" class="form-control" type="text" value="<?php echo $customer->amocrm_contact_id; ?>" /></td>
        </tr>
        <tr>
            <td>Amo lead</td>
            <td><input name="amocrm_lead_id" class="form-control" type="text" value="<?php echo $customer->amocrm_lead_id; ?>" /></td>
        </tr>
        <tr>
            <td>Telegram username</td>
            <td><input name="telegram_username" class="form-control" type="text" value="<?php echo $customer->telegram_username; ?>" /></td>
        </tr>
        <tr>
            <td>Telegram channel role</td>
            <td>
                <select name="telegram_role" class="form-control">
                    <option value="">Other</option>
                    <option value="creator" <?php echo ($customer->telegram_role === 'creator' ? ' selected ' : ''); ?>>creator</option>
                    <option value="admin" <?php echo ($customer->telegram_role === 'admin' ? ' selected ' : ''); ?>>admin</option>
                    <option value="user" <?php echo ($customer->telegram_role === 'user' ? ' selected ' : ''); ?>>user</option>
                    <option value="banned" <?php echo ($customer->telegram_role === 'banned' ? ' selected ' : ''); ?>>banned</option>
                    <option value="canceled" <?php echo ($customer->telegram_role === 'canceled' ? ' selected ' : ''); ?>>canceled</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Vip started</td>
            <td>
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" name="vip_from" class="form-control pull-right datepicker" value="<?php echo (!is_null($customer->vip_from) ? date('Y-m-d', $customer->vip_from) : ''); ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td>Vip till</td>
            <td>
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" name="vip_till" class="form-control pull-right datepicker" value="<?php echo (!is_null($customer->vip_till) ? date('Y-m-d', $customer->vip_till) : ''); ?>" />
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-set-vip-till" type="button">Set</button>
                      </span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Last transaction</td>
            <td><input name="transaction_id" class="form-control" type="text" value="<?php echo $customer->transaction_id; ?>" /></td>
        </tr>
        <tr>
            <td>Transaction currency</td>
            <td>
                <select name="currency_id" class="form-control">
                    <option value="">Other</option>
                    <?php
                    foreach ($currencies as $val) {
                        ?>
                        <option value="<?php echo $val['id']; ?>" <?php echo ($customer->currency_id === $val['id'] ? ' selected ' : ''); ?>><?php echo $val['title']; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Last plan paid</td>
            <td>
                <select name="billing_plan_id" class="form-control">
                    <option value="">Other</option>
                    <?php
                    foreach ($plans as $val) {
                    ?>
                    <option value="<?php echo $val['id']; ?>" <?php echo ($customer->billing_plan_id === $val['id'] ? ' selected ' : ''); ?>><?php echo $val['title']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php
                if ($customer->telegram_role !== 'canceled') {
                    ?>
                    <input type="button" class="btn btn-danger btn-cancel-vip pull-left" data-id="<?php echo $customer->id; ?>" value="Cancel Vip" />
                    <?php
                }
                ?>
                <input type="submit" class="btn btn-primary pull-right" value="Save changes" />
            </td>
        </tr>
    </table>
</form>