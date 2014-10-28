function unixdate(str) {
    var t = str.split('/');
    return new Date(parseInt(t[2]), parseInt(t[1]) - 1, t[0])
}

function hash(array, fn) {
    if (typeof fn === 'string') {
        var key = fn;
        fn = function () {
            return [this[key], this]
        }
    }
    var results = {},
        i = 0,
        len = array.length,
        pair = [];

    for (; i < len; i++) {
        pair = fn.call(array[i], array[i], i, array);
        results[ pair[0] ] = pair[1];
    }

    return results;
}

(function ($) {
    $.fn.serializeObject = function (allowEmptyValues) {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (allowEmptyValues || (this.value !== undefined && this.value !== '')) {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value);
                } else {
                    o[this.name] = this.value;
                }
            }
        });
        return o;
    };


    $.fn.serializeBlock = function () {
        var rselectTextarea = /^(?:select|textarea)/i,
            rinput = /^(?:color|date|datetime|datetime-local|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i,
            rCRLF = /\r?\n/g;

        return this.map(function () {
            var elements = $(this).find('*');

            return $.makeArray(elements);
        })
            .filter(function () {
                return this.name && !this.disabled &&
                    ( this.checked || rselectTextarea.test(this.nodeName) ||
                        rinput.test(this.type) );
            })
            .map(
            function (i, elem) {
                var val = jQuery(this).val();

                return val == null ?
                    null :
                    jQuery.isArray(val) ?
                        jQuery.map(val, function (val, i) {
								return { name: elem.name, value: val.replace(rCRLF, "\r\n") };
                        }) :
                    { name: elem.name, value: val.replace(rCRLF, "\r\n") };
            }).get();
    }

    $.fn.serializeBlock2Object = function () {
        var o = {};
        var a = this.serializeBlock();
        for (var i = 0; i < a.length; i++) {
            if (o[a[i].name] !== undefined) {
                if (!o[a[i].name].push) {
                    o[a[i].name] = [o[a[i].name]];
                }
                o[a[i].name].push(a[i].value || '');
            } else {
                o[a[i].name] = a[i].value || '';
            }
        }
        return o;
    };

    if (/Opera/.test(navigator.userAgent)) {
        $.fn.serializeArray = function () {
            var rselectTextarea = /^(?:select|textarea)/i,
                rinput = /^(?:color|date|datetime|datetime-local|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i,
                rCRLF = /\r?\n/g;

            return this.map(function () {
                var elements = $(this).find('*');

                return $.makeArray(elements);
            })
                .filter(function () {
                    return this.name && !this.disabled &&
                        ( this.checked || rselectTextarea.test(this.nodeName) ||
                            rinput.test(this.type) );
                })
                .map(
                function (i, elem) {
                    var val = jQuery(this).val();

                    return val == null ?
                        null :
                        jQuery.isArray(val) ?
                            jQuery.map(val, function (val, i) {
                                return { name: elem.name, value: val.replace(rCRLF, "\r\n") };
                            }) :
                        { name: elem.name, value: val.replace(rCRLF, "\r\n") };
                }).get();
        }
    }

    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        for (var i = 0; i < a.length; i++) {
            if (o[a[i].name] !== undefined) {
                if (!o[a[i].name].push) {
                    o[a[i].name] = [o[a[i].name]];
                }
                o[a[i].name].push(a[i].value || '');
            } else {
                o[a[i].name] = a[i].value || '';
            }
        }
        return o;
    };

    $.extend({
        parseQuerystring: function (location) {
            var nvpair = {};
            var qs = location.replace(/^\?/, '');
            var pairs = qs.split('&');
            $.each(pairs, function (i, v) {
                var pair = v.split('=');
                if (pair[0]) {
                    nvpair[pair[0]] = pair[1];
                }
            });
            return nvpair;
        }
    });

})(jQuery);