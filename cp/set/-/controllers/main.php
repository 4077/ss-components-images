<?php namespace ss\components\images\cp\set\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        $this->cat = $this->data('cat');

        $this->instance_($this->cat->id);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $images = \ss\components\images\cat($cat)->imagesBuilder()->orderBy('position')->get();

        foreach ($images as $image) {
            $v->assign('image', [
                'ID'      => $image->id,
                'CONTENT' => $this->c('>image:view', [
                    '~'       => true,
                    'cat'     => $this->cat,
                    'image' => $image
                ])
            ]);
        }

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('. .images'),
            'items_id_attr'  => 'image_id',
            'path'           => '>xhr:arrange',
            'plugin_options' => [
                'distance' => 20,
                'handle'   => '.sortable_handle',
                'axis'     => 'y'
            ]
        ]);

        $v->assign([
                       'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'data'    => [
                               'cat' => xpack_model($cat)
                           ],
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ]),
                       //                       'CREATE_FROM_IMAGES_BUTTON' => $this->c('\std\ui button:view', [
                       //                           'path'    => '>xhr:create',
                       //                           'data'    => [
                       //                               'set' => xpack_model($set)
                       //                           ],
                       //                           'class'   => 'create_from_images_button',
                       //                           'content' => 'Создать из картинок'
                       //                       ])
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:ss/components/images');

        $this->css(':\css\std~, \js\jquery\ui icons');

        $this->widget(':|', [
            '.r'    => [
                'reload'        => $this->_abs('>xhr:reload', [
                    'cat' => $catXPack
                ]),
                'reloadImage' => $this->_abs('>xhr:reloadImage')
            ],
            'catId' => $cat->id
        ]);

        return $v;
    }
}
