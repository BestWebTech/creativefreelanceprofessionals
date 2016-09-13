/**
 * @project et_mailing
 * @author thuytien
 * @date 02/03/2015
 */
(function(Models, Views, Collections, $, Backbone) {
    $(document).ready(function() {
        //var categories = new Views.CategoryList();
        var options = new Models.Options();
        /**
         * settings control init
         */
        if ($('#settings').length > 0) {
            var options_view = new Views.Options({
                el: '#settings',
                model: options
            });
        }
    });
})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);