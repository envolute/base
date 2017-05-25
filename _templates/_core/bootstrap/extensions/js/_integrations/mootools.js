// MOOTOOLS INTEGRATION
// Integração com a biblioteca 'mootools'

// Desabilita recursos do Mootools quando o Bootstrap é carregado
(function($) {
    $(document).ready(function(){
        var bootstrapLoaded = (typeof $().tooltip == 'function' || typeof $().carousel == 'function');
        var mootoolsLoaded = (typeof MooTools != 'undefined');
        if (bootstrapLoaded && mootoolsLoaded) {
            Element.implement({
                hide: function () {
                    return this;
                },
                show: function (v) {
                    return this;
                },
                slide: function (v) {
                    return this;
                }
            });
        }
    });
})(jQuery);
