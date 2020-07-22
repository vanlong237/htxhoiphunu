jQuery(document).ready(function($) {

    if ($('#Btloco_rollback.button').length > 0) {
        $('#Btloco_rollback.button').on('click', function() {
            $('.atlt_rollbakBtn .notices').html('');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action': 'atlt_rollback_request',
                    'key': atlt_rollback.key
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.addEventListener("progress", function(et) {
                        $('.atlt_rollbakBtn .notices').html($.parseHTML(et.currentTarget.responseText));
                    }, false);
                    return xhr;
                },
                beforeSend: function(XMLHttpRequest) {
                    $('.atlt_rollbakBtn .notices').text('Wait while loco translate rollback in progress...');
                    $('#Btloco_rollback.button').text('Rolling Back...');
                },
                complete: function(res) {
                    window.setTimeout(
                        function() {
                            window.location.reload()
                        }, 600
                    );
                }
            })

        });
    };

});