<?php echo form_open(get_uri("restapi/manage"), ["id" => "restapi-form", "class" => "general-form", "role" => "form"]); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
    <div id="errors"></div>
      <?php
		 if (isset($model_info->id)) {
		 	echo form_hidden('id', $model_info->id);
		 }
	   ?>       

        <div class="form-group">
            <div class="row">
                <label for="user" class="col-md-3"><?php echo app_lang('api_user'); ?></label>
                <div class="col-md-9">
                    <?php
					echo form_input([
						"id"                 => "user",
						"name"               => "user",
						"value"              => $model_info->user ?? "",
						"class"              => "form-control",
						"autofocus"          => true,
						"placeholder"        => app_lang('enter_useremail'),
						"data-rule-required" => true,
						"data-msg-required"  => app_lang("field_required"),
					]);
					?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="name" class=" col-md-3"><?php echo app_lang('api_name'); ?></label>
                <div class=" col-md-9">
                    <?php
					echo form_input([
						"id"                 => "name",
						"name"               => "name",
						"value"              => $model_info->name ?? "",
						"class"              => "form-control",
						"autofocus"          => true,
						"placeholder"        => app_lang("enter_username"),
						"data-rule-required" => true,
						"data-msg-required"  => app_lang("field_required"),
					]);
					?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="expiration_date" class=" col-md-3"><?php echo app_lang('expiration_date'); ?></label>
                <div class="col-md-9">
                    <?php
					echo form_input([
						"id"                 => "expiration_date",
						"name"               => "expiration_date",
						"value"              => $model_info->expiration_date ?? "",
						"class"              => "form-control",
						"placeholder"        => "",
						"autocomplete"       => "off",
						"data-rule-required" => true,
						"data-msg-required"  => app_lang("field_required"),
						'readonly'           => true
					]);
					?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>
        <button class="btn btn-primary btn_save"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";
    $(function(){
        setDatePicker("#expiration_date");

        $('#restapi-form').on('submit', function(e){
            e.preventDefault();

            // Serialize form data
            var dataString = $(this).serialize();

            // Convert date format to MySQL datetime format
            var expiration_date = $('#expiration_date').val();
            var formatted_date = formatDateForMySQL(expiration_date);

            if (formatted_date) {
                dataString += '&expiration_date=' + formatted_date;
            } else {
                console.error("Invalid date format:", expiration_date);
                return; // Exit if the date format is invalid
            }

            var request_url = $(this).attr('action');
            $.ajax({
                url: request_url,
                type: 'POST',
                dataType: 'json',
                data: dataString,
                beforeSend: function() {
                    $('.btn_save').prop('disabled', true);
                }
            })
            .done(function(response) {
                if (response.success === "frm_error") {
                    $('#errors').html(response.message).addClass('alert alert-danger');
                } else {
                    appAlert.success(response.message, { duration: 10000 });
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        });

        // Function to format date for MySQL datetime format
        function formatDateForMySQL(dateString) {
            // Assuming dateString is in 'yyyy-mm-dd' format
            var dateParts = dateString.split("-");
            if (dateParts.length === 3) {
                // Format for MySQL: YYYY-MM-DD HH:MM:SS
                var formattedDate = dateParts[0] + "-" + dateParts[1] + "-" + dateParts[2] + " 00:00:00";
                return formattedDate;
            } else {
                // Return null if the format is not as expected
                return null;
            }
        }
    });
</script>
