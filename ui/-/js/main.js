// head {
var __nodeId__ = "ss_components_images_ui__main";
var __nodeNs__ = "ss_components_images_ui";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, $.ewma.node, {
        options: {},

        __create: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bind();
            w.bindEvents();
        },

        bind: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.bindJsLinkBlocks();
        },

        bindJsLinkBlocks: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            $("[block]", $w).rebind("click", function () {
                var $block = $(this);

                var block = $block.attr("block");

                if (o.jsLinkBlocks.indexOf(block) > -1) {
                    var $image;

                    if (block === 'block') {
                        $image = $block;
                    } else {
                        $image = $block.closest("[block='block']")
                    }

                    var link = $image.data("link");

                    if (link) {
                        if (link.mode === 'cat' || link.mode === 'route' || link.mode === 'url') {
                            window.location.href = link.value;
                        }

                        if (link.mode === 'anchor') {
                            window.location.href = link.value; // todo test
                        }
                    }
                }
            });
        },

        bindEvents: function () {
            var w = this;
            var o = w.options;
            var $w = w.element;

            w.e('ss/components/images/update', function (data) {
                if (o.catId === data.catId) {
                    // w.mr('reload');
                    ewma.trigger('ss/cat/components_update.' + o.catId);

                    w.bindJsLinkBlocks();
                }
            });

            w.e('ss/components/images/image/update', function (data) {
                var $image = $(".image[image_id='" + data.id + "']", $w);

                if ($image.length) {
                    w.mr('reloadImage', {
                        id: data.id
                    });
                }
            });
        }
    });
})(__nodeNs__, __nodeId__);
