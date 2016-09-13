(function($, Views) {
    Views.PayfForm = Views.Modal_Box.extend({
        el: $('div#payf_modal'),
        events: {
            'submit form#payf_form': 'submitPayf'
        },
        initialize: function(options) {
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            // bind event to modal
            _.bindAll(this, 'setupData');
            this.blockUi = new Views.BlockUi();
            // catch event select extend gateway
            AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
        },
        // callback when user select Paymill, set data and open modal
        setupData: function(data) {
            if (data.paymentType == 'payf') {
                this.openModal();
                this.data = data,
                plans = JSON.parse($('#package_plans').html());
                var packages = [];
                _.each(plans, function(element) {
                    if (element.sku == data.packageID) {
                        packages = element;
                    }
                })
              var align = parseInt(ae_payf.currency.align);
                if (align) {
                    var price = ae_payf.currency.icon + packages.et_price;
                } else {
                    var price = packages.et_price + ae_payf.currency.icon;
                }

                this.data.price = packages.et_price;

                //if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) price  =   $('.coupon-price').html();
                this.$el.find('span.plan_name').html(packages.post_title + ' (' + price + ')');
                this.$el.find('span.plan_desc').html(packages.post_content);
            }
        },
        // catch user event click on pay
        submitPayf: function(event) {
            event.preventDefault();
            var form = $(event.currentTarget),
                data = this.data;
                data.payf_name_first = form.find('#payf_name_first').val();
                data.payf_name_last  = form.find('#payf_name_last').val();
                data.payf_email_address     = form.find('#payf_email_address').val();
                data.payf_phone     = form.find('#payf_phone').val();
                //console.log(data.payf_name_first);
                //console.log(data.payf_email_address);
                if(data.payf_name_first == "" && data.payf_email_address == ""){
                    alert("error");
                    return false;
                }
             var view = this;   
                //neu muon post sang setup payment
            $.ajax({
                // gui du lieu sang ben payf setup du lieu page
                // setup page
                type : 'post',
                url : ae_globals.ajaxURL,
                // tra ve nguyen 1 cuc data
                data : data,
                 beforeSend: function() {
                    view.blockUi.block('#button_payf');
                },
                success:function(res){
                    console.log(res);
                   // view.blockUi.unblock();
                    if(res.success){
                        //return false;
                    //alert(res.data.salt);
                    // gan du lieu vao ID
                       /* $('#payf_hash').val(res.data.hash);
                        $('#payf_merchant_id').val(res.data.idh);
                        //$('#payf_item_name').val(res.data.item_name);
                         //$('#payf_m_payment_id').val(res.data.m_payment_id);
                         $('#payf_item_name').val(res.data.item_name);
    
                        $('#payf_merchant_key').val(res.data.key);
                        
                        $("#payf_amount").val(res.data.amount);
                        $("#payf_name_first_h").val(res.data.name_first);
                        $("#payf_name_last_h").val(res.data.name_last);
                        $("#payf_email_address_h").val(res.data.email_address);
                        $("#payf_phone").val(res.data.phone);
                        $("#payf_productinfo").val(res.data.productinfo);
                        $("#payf_m_payment_id").val(res.data.m_payment_id);         
                        $('#payf_return_url').val(res.data.return_url);
                        
                        $('#payf_cancel_url').val(res.data.cancel_url);
                        
                        $('#payf_notify_url').val(res.data.notify_url);
                        console.log(res.data.idh);
                        console.log(res.data.return_url);
                        console.log(res.data.notify_url);
                        console.log(res.data.cancel_url);
                        $("#payf_hidden_form").attr("action", res.data.url);
                        $('#item_name').val('customname');*/
                        $('#button_payf_h').trigger("click");
                        return false;
                    }else{
                        AE.pubsub.trigger('ae:notification', {
                            msg: ae_payf.errorpost,
                            notice_type: 'error'
                        });
                        return false
                    }
                }
                
            });
        },
    });
    // init Payf form
    $(document).ready(function() {
        new Views.PayfForm();
    });
})(jQuery, AE.Views);

function ae_payf_validateEmail(email) {   
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([\w\W\-0-9]+\.)+[\w\W]{2,}))$/;
    return re.test(email);
}
jQuery('#button_payf').click(function(){
    if(jQuery("#payf_name_first").val()==""){
         AE.pubsub.trigger('ae:notification', {
            msg: ae_payf.empty_field,
            notice_type: 'error'
        });
        jQuery("#payf_name_first").focus();
        return false    
    }
    $email = jQuery("#payf_email_address").val();
    if($email == ""){
        AE.pubsub.trigger('ae:notification', {
            msg: ae_payf.empty_field,
            notice_type: 'error'
        });
        if(ae_payf_validateEmail($email)){
            AE.pubsub.trigger('ae:notification', {
            msg: ae_payf.email_error,
            notice_type: 'error'
        });
            
        }        
        jQuery("#payf_email_address").focus();
        return false    
    }
})
