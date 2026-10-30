<script type="text/javascript">
    function showAjaxModal(url, header) {
        // SHOWING AJAX PRELOADER IMAGE
        jQuery('#scrollable-modal .modal-body').html('<div style="width: 100px; height: 100px; line-height: 100px; padding: 0px; text-align: center; margin-left: auto; margin-right: auto;"><div class="spinner-border text-secondary" role="status"></div></div>');
        jQuery('#scrollable-modal .modal-title').html('loading...');
        // LOADING THE AJAX MODAL
        jQuery('#scrollable-modal').modal('show', {
            backdrop: 'true'
        });

        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        $.ajax({
            url: url,
            success: function(response) {
                jQuery('#scrollable-modal .modal-body').html(response);
                jQuery('#scrollable-modal .modal-title').html(header);
            }
        });
    }

    function showLargeModal(url, header) {
        // SHOWING AJAX PRELOADER IMAGE
        jQuery('#large-modal .modal-body').html('<div style="width: 100px; height: 100px; line-height: 100px; padding: 0px; text-align: center; margin-left: auto; margin-right: auto;"><div class="spinner-border text-secondary" role="status"></div></div>');
        jQuery('#large-modal .modal-title').html('...');
        // LOADING THE AJAX MODAL
        jQuery('#large-modal').modal('show', {
            backdrop: 'true'
        });

        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        $.ajax({
            url: url,
            success: function(response) {
                jQuery('#large-modal .modal-body').html(response);
                jQuery('#large-modal .modal-title').html(header);
            }
        });
    }

    function changeTitleOfImageUploader(photoElem) {
        var fileName = $(photoElem).val().replace(/C:\\fakepath\\/i, '');
        $(photoElem).siblings('label').text(ellipsis(fileName));
    }

    function ellipsis(str, length, ending) {
        if (length == null) {
            length = 40;
        }
        if (ending == null) {
            ending = '...';
        }
        if (str.length > length) {
            return str.substring(0, length - ending.length) + ending;
        } else {
            return str;
        }
    };
</script>


<div class="modal fade" id="large-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                ...
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="scrollable-modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollableModalTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ml-2 mr-2">

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function confirm_modal(delete_url) {
        jQuery('#alert-modal').modal('show', {
            backdrop: 'static'
        });
        document.getElementById('update_link').setAttribute('href', delete_url);
    }
</script>


<div id="alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-information h1 text-info"></i>
                    <h4 class="mt-2">Heads up!</h4>
                    <p class="mt-3">Are you sure?</p>
                    <button type="button" class="btn btn-info my-2" data-dismiss="modal">Cancel</button>
                    <a href="#" id="update_link" class="btn btn-danger my-2">Continue</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function ajax_confirm_modal(delete_url, elem_id) {
        jQuery('#ajax-alert-modal').modal('show', {
            backdrop: 'static'
        });
        $("#appent_link_a").bind("click", function() {
            delete_by_ajax_calling(delete_url, elem_id);
        });
    }

    function delete_by_ajax_calling(delete_url, elem_id) {
        $.ajax({
            url: delete_url,
            success: function(response) {
                var response = JSON.parse(response);
                if (response.status == 'success') {
                    $('#' + elem_id).fadeOut(600);
                    $.NotificationApp.send("Success!", response.message, "top-right", "rgba(0,0,0,0.2)", "success");
                } else {
                    $.NotificationApp.send("Oh snap!", response.message, "top-right", "rgba(0,0,0,0.2)", "error");
                }
            }
        });
    }
</script>


<div id="ajax-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center" id="appent_link">
                    <i class="dripicons-information h1 text-info"></i>
                    <h4 class="mt-2">Heads up!</h4>
                    <p class="mt-3">Are you sure?</p>
                    <button type="button" class="btn btn-info my-2" data-dismiss="modal">Cancel</button>
                    <a id="appent_link_a" href="javascript:;" class="btn btn-danger my-2" data-dismiss="modal">Continue?</a>
                </div>
            </div>
        </div>
    </div>
</div>