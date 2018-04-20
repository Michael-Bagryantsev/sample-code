<table class="table table-bordered">

    <!-- General -->
    <tr>
        <td colspan="2"><b>General</b></td>
    </tr>
    <tr>
        <td>Id</td>
        <td><?php echo $customer->id; ?></td>
    </tr>
    <tr>
        <td>Firstname</td>
        <td><?php echo $customer->first_name; ?></td>
    </tr>
    <tr>
        <td>Lastname</td>
        <td><?php echo $customer->last_name; ?></td>
    </tr>
    <tr>
        <td>From</td>
        <td><?php echo $customer->customer_from; ?></td>
    </tr>
    <tr>
        <td>Is Vip</td>
        <td><?php echo ($customer->is_vip === 1 ? 'Yes' : 'No'); ?></td>
    </tr>
    <tr>
        <td>Unsubscribed</td>
        <td><?php echo ($customer->is_blocked === 1 ? 'Yes' : 'No'); ?></td>
    </tr>
    <!-- /General -->

    <!-- Amo -->
    <tr>
        <td colspan="2"><b>Amo</b></td>
    </tr>
    <tr>
        <td>Contact id</td>
        <td><?php echo (!empty($customer->amocrm_contact_id) ? '<a href="https://topico.amocrm.ru/contacts/detail/' . $customer->amocrm_contact_id . '" target="blank">' . $customer->amocrm_contact_id . '</a>' : ''); ?></td>
    </tr>
    <tr>
        <td>Lead id</td>
        <td><?php echo (!empty($customer->amocrm_lead_id) ? '<a href="https://topico.amocrm.ru/leads/detail/' . $customer->amocrm_lead_id . '" target="blank">' . $customer->amocrm_lead_id . '</a>' : ''); ?></td>
    </tr>
    <tr>
        <td>Chat Status</td>
        <td><?php echo $customer->customer_chat_status; ?></td>
    </tr>
    <!-- /Amo -->

    <!-- Source -->
    <tr>
        <td colspan="2"><b>Source</b></td>
    </tr>
    <tr>
        <td>Source</td>
        <td><?php echo $customer->utm_source; ?></td>
    </tr>
    <tr>
        <td>Ohid</td>
        <td><?php echo $customer->ohid; ?></td>
    </tr>
    <tr>
        <td>Ref</td>
        <td><?php echo $customer->utm_channel; ?></td>
    </tr>
    <tr>
        <td>Plan</td>
        <td><?php echo $customer->utm_plan; ?></td>
    </tr>
    <!-- /Source -->

    <!-- Billing -->
    <tr>
        <td colspan="2"><b>Billing</b></td>
    </tr>
    <tr>
        <td>Currecy</td>
        <td><?php echo $customer->code; ?></td>
    </tr>
    <tr>
        <td>Plan</td>
        <td><?php echo $customer->plan_name; ?></td>
    </tr>
    <tr>
        <td>Wallet</td>
        <td><?php echo $customer->address; ?></td>
    </tr>
    <tr>
        <td>Status</td>
        <td><?php echo $customer->billing_status; ?></td>
    </tr>
    <tr>
        <td>Created</td>
        <td><?php echo $customer->date_created; ?></td>
    </tr>
    <!-- /Billing -->

    <!-- Facebook -->
    <tr>
        <td colspan="2"><b>Facebook</b></td>
    </tr>
    <tr>
        <td>Locale</td>
        <td><?php echo $customer->locale; ?></td>
    </tr>
    <tr>
        <td>Sex</td>
        <td><?php echo $customer->sex; ?></td>
    </tr>
    <!-- /Facebook -->

    <!-- Telegram -->
    <tr>
        <td colspan="2"><b>Telegram</b></td>
    </tr>
    <tr>
        <td>Username</td>
        <td><?php echo $customer->telegram_username; ?></td>
    </tr>
    <!-- /Telegram -->

    <!-- History -->
    <tr>
        <td colspan="2"><b>History</b></td>
    </tr>
    <tr>
        <td>Date registered</td>
        <td><?php echo $customer->date_registered; ?></td>
    </tr>
    <tr>
        <td>Lead created</td>
        <td><?php echo $customer->lead_created; ?></td>
    </tr>
    <tr>
        <td>Lead manager</td>
        <td><?php echo $customer->lead_manager; ?></td>
    </tr>
    <tr>
        <td>Lead completed</td>
        <td><?php echo $customer->lead_completed; ?></td>
    </tr>
    <tr>
        <td>First Message</td>
        <td><?php echo $customer->date_first_message; ?></td>
    </tr>
    <tr>
        <td>Last Message</td>
        <td><?php echo $customer->date_last_message; ?></td>
    </tr>
    <!-- /History -->
</table>