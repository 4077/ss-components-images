// head {
var __nodeId__ = "ss_components_images_cp_set__main_image";
var __nodeNs__ = "ss_components_images_cp_set";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bindEvents();
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/components/images/image/update', function (data) {
                if (o.id === data.id) {
                    w.mr('reload');
                }
            });

            w.e('ss/components/images/image/toggle_enabled', function (data) {
                if (o.id === data.id) {
                    w.mr('reload');
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
