/**
 * Wrapper function to safely use $
 */
var custom_uploader;
function wpsmAdminWrapper($) {
    var wpsmAdmin = {
        /**
         * Main entry point
         */
        init: function () {
        }
    }; // end wpsmAdmin

    $(document).ready(wpsmAdmin.init);

} // end wpsmAdminWrapper()

wpsmAdminWrapper(jQuery);
