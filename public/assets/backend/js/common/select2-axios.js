(function (window, $) {
    'use strict';

    if (typeof $ === 'undefined') {
        throw new Error('jQuery must be loaded before select2-axios.js');
    }

    function baseConfig(element, options) {
        var $element = $(element);
        var dropdownParent = $element.closest('.modal');
        var config = {
            width: '100%',
            allowClear: Boolean(options.allowClear),
            placeholder: options.placeholder || 'Select an option'
        };

        if (Object.prototype.hasOwnProperty.call(options, 'closeOnSelect')) {
            config.closeOnSelect = Boolean(options.closeOnSelect);
        }

        if (dropdownParent.length) {
            config.dropdownParent = dropdownParent;
        }

        return config;
    }

    function mountSelect2(element, config) {
        var $element = $(element);

        if ($element.hasClass('select2-hidden-accessible')) {
            $element.select2('destroy');
        }

        $element.select2(config);
    }

    window.appSelect2 = {
        static: function (element, options) {
            options = options || {};
            mountSelect2(element, baseConfig(element, options));
        },

        ajax: function (element, options) {
            options = options || {};

            var config = baseConfig(element, options);

            config.ajax = {
                url: options.url,
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                transport: function (params, success, failure) {
                    var controller = new AbortController();

                    window.appAxios.get(params.url, {
                        params: params.data,
                        signal: controller.signal
                    }).then(function (response) {
                        success(response.data);
                    }).catch(function (error) {
                        if (error.code !== 'ERR_CANCELED') {
                            failure(error);
                        }
                    });

                    return {
                        abort: function () {
                            controller.abort();
                        }
                    };
                },
                processResults: function (data) {
                    return data;
                }
            };

            mountSelect2(element, config);
        }
    };

    window.initAxiosSelect2 = function (element, options) {
        window.appSelect2.ajax(element, options || {});
    };
})(window, window.jQuery);
