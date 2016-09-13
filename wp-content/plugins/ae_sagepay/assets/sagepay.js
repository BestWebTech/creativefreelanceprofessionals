(function($, Views) {
    Views.Sagepay = Views.Modal_Box.extend({
        el: $('div#sagepay_modal'),
        events: {
            'submit form#sagepay_form': 'submitPayment'
        },
        initialize: function(options) {
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            // bind event to modal
            _.bindAll(this, 'setupData');
            this.blockUi = new Views.BlockUi();
            // catch event select extend gateway
            AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
            this.initValidator();
        },
        
        initValidator:function(){
            if(typeof this.sagepay_validate === "undefined"){
                this.sagepay_validate = this.$("form#sagepay_form").validate({
                    rules: {
                        sagepay_firstname: "required",
                        sagepay_lastname: "required",
                        sagepay_billingadress: "required",
                        sagepay_billingcity : "required",
                        sagepay_country : "required"
                    }
                })
            }
        },
        
        // callback when user select Paymill, set data and open modal
        setupData: function(data) {
            if (data.paymentType == 'sagepay') {
                this.openModal();
                this.data = data,
                plans = JSON.parse($('#package_plans').html());
                var packages = [];
                _.each(plans, function(element) {
                    if (element.sku == data.packageID) {
                        packages = element;
                    }
                })
              var align = parseInt(ae_sagepay.currency.align);
                if (align) {
                    var price = ae_sagepay.currency.icon + packages.et_price;
                } else {
                    var price = packages.et_price + ae_sagepay.currency.icon;
                }

                this.data.price = packages.et_price;
                //console.log(packages);
                //if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) price  =   $('.coupon-price').html();
                this.$el.find('span.plan_name').html(packages.post_title + ' (' + price + ')');
                this.$el.find('span.plan_desc').html(packages.post_content);
            }
        },
        // catch user event click on pay
        submitPayment: function(event) {
            event.preventDefault();
            if(this.sagepay_validate.form()){
                var $form = $(event.currentTarget),
                   
                    data = this.data;
                    
                     //end validate form
                    
                    //set value to data
                    data.sagepay_firstname          = $form.find('#sagepay_firstname').val();
                    data.sagepay_lastname           = $form.find('#sagepay_lastname').val();
                    data.sagepay_billingadress      = $form.find('#sagepay_billingadress').val();
                    data.sagepay_country            = $form.find('#sagepay_country').val();
                    data.sagepay_state              = $form.find('#sagepay_state').val();
                    data.sagepay_postcode           = $form.find('#sagepay_postcode').val();
                    data.sagepay_billingcity        = $form.find('#sagepay_billingcity').val();
                    /**
                     * Validate form 
                     */
                    if( data.sagepay_firstname == "" ||
                        data.sagepay_lastname == "" ||
                        data.sagepay_billingadress == "" ||
                        data.sagepay_country == "" ||
                        sagepay_state == "" ||
                        data.sagepay_postcode == "" ||
                        data.sagepay_billingcity == "" 
                        )
                    {
                        AE.pubsub.trigger('ae:notification', {
                            msg: ae_sagepay.not_emty,
                            notice_type: 'error'
                        });
                        return false;
                    }
                    /**
                     * if country is US the state must be set value
                     */ 
                    if( data.sagepay_country == 'US' ){
                        if( data.sagepay_state == "" ){
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_sagepay.not_emty_state,
                                notice_type: 'error'
                            }); 
                            return false;
                        }
                    }
                        
                 var view = this;   
                  
                    //neu muon post sang setup payment
                $.ajax({
                    type : 'post',
                    url : ae_globals.ajaxURL,
                    data : data,
                    beforeSend: function() {
                        view.blockUi.block('#button_sagepay');
                    },
                    success:function(res){
                        if(!res.success){
                            view.blockUi.unblock('#button_sagepay');
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_sagepay.not_emty,
                                notice_type: 'error'
                            });
                            return false;    
                        }else{
                            $("input[name='Vendor']").val(res.vendor);
                            $("input[name='Crypt']").val(res.crypt);
                            $('#sagepay_hidden_form').attr("action", res.link);
                            //console.log(res.product);
                            $('#submit_sagepay').trigger("click");
                        }
                    }
                    
                });
            }
        },
    });
    // init Payu form
    $(document).ready(function() {
        new Views.Sagepay();
    });
})(jQuery, AE.Views);


