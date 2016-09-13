// declare everything inside this object
window.AE = window.AE || {};
(function(AE, $, Backbone) {
    AE.Models = AE.Models || {};
    AE.Collections = AE.Collections || {};
    AE.Views = AE.Views || {};
    AE.Routers = AE.Routers || {};
    // the pub/sub object for managing event throughout the app
    AE.pubsub = AE.pubsub || {};
    _.extend(AE.pubsub, Backbone.Events);
    AE.globals = ae_globals;
    /**
     * override backbone sync function
     */
    Backbone.Model.prototype.sync = function(method, model, options) {
        var data = model.attributes;
        data.action = model.action || 'ae-sync';
        switch (method) {
            case 'create':
                data.method = 'create';
                break;
            case 'update':
                data.method = 'update';
                break;
            case 'delete':
                data.method = 'remove';
                break;
            case 'read':
                data.method = 'read';
                break;
        }
        var ajaxParams = {
            type: 'POST',
            dataType: 'json',
            data: data,
            url: ae_globals.ajaxURL,
            contentType: 'application/x-www-form-urlencoded;charset=UTF-8'
        };
        ajaxParams = _.extend(ajaxParams, options);
        if (options.beforeSend !== 'undefined') ajaxParams.beforeSend = options.beforeSend;
        ajaxParams.success = function(result, status, jqXHR) {
            AE.pubsub.trigger('ae:success', result, status, jqXHR);
            options.success(result, status, jqXHR);
        };
        ajaxParams.error = function(jqXHR, status, errorThrown) {
            AE.pubsub.trigger('ae:error', jqXHR, status, errorThrown);
            options.error(jqXHR, status, errorThrown);
        };
        $.ajax(ajaxParams);
    };
    /**
     * override backbone collection sync
     */
    Backbone.Collection.prototype.sync = function(method, collection, options) {
        var ajaxParams = {
            type: 'POST',
            dataType: 'json',
            data: {},
            url: ae_globals.ajaxURL,
            contentType: 'application/x-www-form-urlencoded;charset=UTF-8'
        };
        ajaxParams.data = _.extend(ajaxParams.data, options.data);
        if (typeof collection.action !== 'undefined') {
            ajaxParams.data.action = collection.action;
        }
        /**
         * add beforsend function
         */
        if (options.beforeSend !== 'undefined') ajaxParams.beforeSend = options.beforeSend;
        /**
         * success function
         */
        ajaxParams.success = function(result, status, jqXHR) {
            AE.pubsub.trigger('ae:success', result, status, jqXHR);
            options.success(result, status, jqXHR);
        };
        ajaxParams.error = function(jqXHR, status, errorThrown) {
            AE.pubsub.trigger('ae:error', jqXHR, status, errorThrown);
            options.error(jqXHR, status, errorThrown);
        };
        $.ajax(ajaxParams);
        // console.log(collection.getAction());
    }
    /**
     * override backbone model parse function
     */
    Backbone.Model.prototype.parse = function(result) {
        if (_.isObject(result.data)) {
            return result.data;
        } else {
            return result;
        }
    };
    /**
     * override backbone model parse function
     */
    Backbone.Collection.prototype.parse = function(result) {
        if (_.isObject(result) && _.isObject(result.data)) {
            return result.data;
        } else {
            return [];
        }
    };
    // create a shorthand for our pubsub
})(window.AE, jQuery, Backbone);
// build basic view
(function(AE, $, Backbone, Views, Models, Collections) {
    // create a shorthand for the params used in most ajax request
    AE.ajaxParams = {
        type: 'POST',
        dataType: 'json',
        url: AE.globals.ajaxURL,
        contentType: 'application/x-www-form-urlencoded;charset=UTF-8'
    };
    var ajaxParams = AE.ajaxParams;
    /**
     * loading effec view
     */
    AE.Views.LoadingEffect = Backbone.View.extend({
        initialize: function() {},
        render: function() {
            this.$el.html(AE.globals.loadingImg);
            return this;
        },
        finish: function() {
            this.$el.html(AE.globals.loadingFinish);
            var view = this;
            setTimeout(function() {
                view.$el.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 1000);
        },
        remove: function() {
            view.$el.remove();
        }
    });
    /**
     * blockui view
     * block an Dom Element with loading image
     */
    AE.Views.BlockUi = Backbone.View.extend({
        defaults: {
            image: AE.globals.imgURL + '/loading.gif',
            opacity: '0.5',
            background_position: 'center center',
            background_color: '#ffffff'
        },
        isLoading: false,
        initialize: function(options) {
            //var defaults = _.clone(this.defaults);
            options = _.extend(_.clone(this.defaults), options);
            var loadingImg = options.image;
            this.overlay = $('<div class="loading-blur loading"><div class="loading-overlay"></div><div class="loading-img"></div></div>');
            this.overlay.find('.loading-img').css({
                'background-image': 'url(' + options.image + ')',
                'background-position': options.background_position
            });
            this.overlay.find('.loading-overlay').css({
                'opacity': options.opacity,
                'filter': 'alpha(opacity=' + options.opacity * 100 + ')',
                'background-color': options.background_color
            });
            this.$el.html(this.overlay);
            this.isLoading = false;
        },
        render: function() {
            this.$el.html(this.overlay);
            return this;
        },
        block: function(element) {
            var $ele = $(element);
            // if ( $ele.css('position') !== 'absolute' || $ele.css('position') !== 'relative'){
            //         $ele.css('position', 'relative');
            // }
            this.overlay.css({
                'position': 'absolute',
                'z-index': 2000,
                'top': $ele.offset().top,
                'left': $ele.offset().left,
                'width': $ele.outerWidth(),
                'height': $ele.outerHeight()
            });
            this.isLoading = true;
            this.render().$el.show().appendTo($('body'));
        },
        unblock: function() {
            this.$el.remove();
            this.isLoading = false;
        },
        finish: function() {
            this.$el.fadeOut(500, function() {
                $(this).remove();
            });
            this.isLoading = false;
        }
    });
    AE.Views.LoadingButton = Backbone.View.extend({
        dotCount: 3,
        isLoading: false,
        initialize: function() {
            if (this.$el.length <= 0) return false;
            var dom = this.$el[0];
            //if ( this.$el[0].tagName != 'BUTTON' && (this.$el[0].tagName != 'INPUT') ) return false;
            if (this.$el[0].tagName == 'INPUT') {
                this.title = this.$el.val();
            } else {
                this.title = this.$el.html();
            }
            this.isLoading = false;
        },
        loopFunc: function(view) {
            var dots = '';
            for (i = 0; i < view.dotCount; i++) dots = dots + '.';
            view.dotCount = (view.dotCount + 1) % 3;
            view.setTitle(AE.globals.loading + dots);
        },
        setTitle: function(title) {
            if (this.$el[0].tagName === 'INPUT') {
                this.$el.val(title);
            } else {
                this.$el.html(title);
            }
        },
        loading: function() {
            //if ( this.$el[0].tagName != 'BUTTON' && this.$el[0].tagName != 'A' && (this.$el[0].tagName != 'INPUT') ) return false;
            this.setTitle(AE.globals.loading);
            this.$el.addClass('disabled');
            var view = this;
            view.isLoading = true;
            view.dots = '...';
            view.setTitle(AE.globals.loading + view.dots);
            this.loop = setInterval(function() {
                if (view.dots === '...') view.dots = '';
                else if (view.dots === '..') view.dots = '...';
                else if (view.dots === '.') view.dots = '..';
                else view.dots = '.';
                view.setTitle(AE.globals.loading + view.dots);
            }, 500);
        },
        finish: function() {
            var dom = this.$el[0];
            this.isLoading = false;
            clearInterval(this.loop);
            this.setTitle(this.title);
            this.$el.removeClass('disabled');
        }
    });
    // View: Modal Box
    AE.Views.Modal_Box = Backbone.View.extend({
        defaults: {
            top: 100,
            overlay: 0.5
        },
        $overlay: null,
        initialize: function() {
            // bind all functions of this object to itself
            //_.bindAll(this.openModal);
            // update custom options if having any
            this.options = $.extend(this.defaults, this.options);
        },
        /**
         * open modal
         */
        openModal: function() {
            var view = this;
            this.$el.modal('show');
        },
        /**
         * close modal
         */
        closeModal: function(time, callback) {
            var modal = this;
            modal.$el.modal('hide');
            return false;
        },
        /**
         * add block ui, block loading
         */
        loading: function() {
            if (typeof this.blockUi === 'undefined') {
                this.blockUi = new AE.Views.BlockUi();
            }
            this.blockUi.block(this.$el.find('input[type="submit"]'));
        },
        /**
         * finish ajax
         */
        finish: function() {
            this.blockUi.unblock();
        },
        // trigger pubsub error
        error: function(res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'error',
            });
        },
        // trigger pubsub notification success
        success: function(res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'success',
            });
        }
    });
})(window.AE, jQuery, Backbone, window.AE.Views, window.AE.Models, window.AE.Collections);