/**
 * Wrapper function to safely use $
 */
function wpsmWrapper($) {
    var wpsm = {

        /**
         * Main entry point
         */
        init: function () {
            wpsm.prefix = 'wpsm_';
            wpsm.templateURL = $('#template-url').val();
            wpsm.ajaxPostURL = $('#ajax-post-url').val();

            wpsm.registerEventHandlers();
        },

        /**
         * Registers event handlers
         */
        registerEventHandlers: function () {
        }
    }; // end wpsm

    $(document).ready(wpsm.init);

} // end wpsmWrapper()

wpsmWrapper(jQuery);
