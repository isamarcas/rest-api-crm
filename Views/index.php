<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix rounded">
            <h1><?php echo app_lang('api'); ?></h1>
                <div class="title-button-group">
                    <?php
					echo modal_anchor(get_uri("restapi/modal"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('new_user'), ["class" => "btn btn-default", "title" => app_lang('new_user')]);
					?>
                </div>
        </div>
        <div class="table-responsive">
            <table id="api-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";
    $(document).ready(function () {
        $("#api-table").appTable({
            source: '<?php echo_uri("restapi/table"); ?>',
            columns: [
                {title: '<?php echo app_lang("api_user"); ?>'},
                {title: '<?php echo app_lang("name"); ?>'},
                {title: '<?php echo app_lang("token"); ?>', "class": "max-w500"},
                {title: '<?php echo app_lang("expiration_date"); ?>'},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option "}
            ],
            printColumns: [0,1,2,3],
            xlsColumns: [0,1,2,3]
        });
    });
</script>