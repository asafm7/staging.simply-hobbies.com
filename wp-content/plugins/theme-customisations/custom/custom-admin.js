jQuery(document).ready(function ($) {

    /* global customAdminJs */


    // HACK: [-0-] 

    $(document).on('change', '#ywcfav_video_name', function () {
        unescapedVideoName = $(this).val();

        escapedVideoName = unescapedVideoName.replaceAll('"', "_").replaceAll("'", "_");

        $(this).val(escapedVideoName);
    });

    // HACK: [-0-] 


    $(document).on('blur', '.acf-input input[type="text"], .acf-input textarea', function () {
        var input = $(this);

        var inputValue = input.val();

        var regex = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/

        var isUrl = regex.test(inputValue);

        if (isUrl) {
            var dataName = input.closest('.acf-field').attr('data-name');

            if (dataName === 'site_name' || dataName === 'site_title' || dataName === 'site_description') {
                var action = "update_value_" + dataName;

                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: customAdminJs.ajaxurl,
                    data: { action: action, input_value: inputValue },
                })
                    .done(function (response) {
                        var value = response.value;

                        input.val(value);
                    })
                    .fail(function () {
                    })
                    .always(function () {
                    });
            }
        }

    });
});
