(function (window, $) {
    'use strict';

    if (typeof $ === 'undefined') {
        throw new Error('jQuery must be loaded before select2-axios.js');
    }

    window.initAxiosSelect2 = function (element, options) {
        var $element = $(element);
        var dropdownParent = $element.closest('.modal');
        var config = {
            width: '100%',
            allowClear: Boolean(options.allowClear),
            placeholder: options.placeholder || 'Select an option',
            ajax: {
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
            }
        };

        if (dropdownParent.length) {
            config.dropdownParent = dropdownParent;
        }

        if ($element.hasClass('select2-hidden-accessible')) {
            $element.select2('destroy');
        }

        $element.select2(config);
    };
})(window, window.jQuery);
