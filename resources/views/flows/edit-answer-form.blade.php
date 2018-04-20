<form action="" name="modal-edit-answer-form" method="post">
    <input name="id" type="hidden" value="<?php echo $answer->id; ?>" />
    <div class="alert hidden"></div>
    <table class="table table-bordered">
        <tr>
            <td>Answer Title</td>
            <td><input name="answer_title" class="form-control" type="text" value="<?php echo $answer->answer_title; ?>" /></td>
        </tr>
        <tr>
            <td colspan="2"><b>Messages</b></td>
        </tr>
        <?php
        foreach ($messages as $message) {
            ?>
             <tr class="answer-new-row">
                 <td colspan="2">
                    <div class="form-group">
                        <textarea id="message_text_<?php echo $message->id; ?>" name="message_text[<?php echo $message->id; ?>]" placeholder="Place some message here" required><?php echo $message->message_text; ?></textarea>
                    </div>
                    <div class="form-group">
                        <input name="message_method[<?php echo $message->id; ?>]" class="form-control" type="text" value="<?php echo $message->message_method; ?>" placeholder="Message method" />
                    </div>
                    <div class="form-group">
                        <input name="message_order[<?php echo $message->id; ?>]" class="form-control" type="number" value="<?php echo $message->message_order; ?>" placeholder="Message order" />
                    </div>
                    <div class="form-group">
                        <input type="button" class="btn btn-danger btn-remove-message pull-right" value="Remove" />
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="2">
                <input type="button" class="btn btn-default btn-add-message pull-right" value="Add message" />
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>Buttons</b></td>
        </tr>
        <?php
        foreach ($buttons as $button) {
            ?>
            <tr class="answer-new-row">
                <td colspan="2">
                    <div class="form-group">
                        <input name="button_text[<?php echo $button->id; ?>]" class="form-control" type="text" value="<?php echo $button->button_text; ?>" placeholder="Button text" />
                    </div>
                    <div class="form-group">
                        <input name="button_order[<?php echo $button->id; ?>]" class="form-control" type="number" value="<?php echo $button->button_order; ?>" placeholder="Button order" />
                    </div>
                    <div class="form-group">
                        <input type="button" class="btn btn-danger btn-remove-button pull-right" value="Remove" />
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="2">
                <input type="button" class="btn btn-default btn-add-button pull-right" value="Add Button" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" class="btn btn-primary pull-right" value="Save answer" />
            </td>
        </tr>
    </table>
</form>