(function (Models, Views, $, Backbone) {
    Views.AEMailSetting = Backbone.View.extend({
        events: {
            'click #send_text_email': 'sendTestEmail',
            'change #test_email': 'changeTestEmail',
            'change #eam_current_service': 'onCurrentServiceChange',
        },
        test_email: "",
        test_email_field: "",
        send_test_button: null,
        initialize: function () {
            var view = this;
            this.test_email_field = $("#test_email", this.$el);
            this.send_test_button = $("#send_text_email", this.$el);

            this.test_email = $("#test_email", this.$el).val();
            this.blockUi = new Views.BlockUi();
        },
        changeTestEmail: function () {
            this.test_email = $("#test_email", this.$el).val();
        },
        onCurrentServiceChange: function (evt) {

        },
        sendTestEmail: function (evt) {
            evt.preventDefault();

            data = {
                action: "aem_test_email",
                test_email: this.test_email
            };
            var view = this;
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    view.blockUi.block(view.send_test_button);
                },
                success: function (rep) {
                    if (rep.message) {
                        alert(rep.message);
                    }
                },
                error: function (e) {

                },
                complete: function () {
                    view.blockUi.unblock();
                }
            });
        }
    });

    jQuery(document).ready(function ($) {
        new Views.AEMailSetting({
            el: $("#aem-general-settings")
        });
    });

})(window.AE.Models, window.AE.Views, jQuery, Backbone);