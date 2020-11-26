jQuery(document).ready(function ($) {

    // HACK: [-0-] 

    $(document).on('change', '#ywcfav_video_name', function () {
        unescapedVideoName = $(this).val();

        escapedVideoName = unescapedVideoName.replaceAll('"', "_").replaceAll("'", "_");

        $(this).val(escapedVideoName);
    });
});
