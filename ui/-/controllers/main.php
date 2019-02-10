<?php namespace ss\components\images\ui\controllers;

class Main extends \Controller
{
    private $cat;

    private $pivot;

    public function __create()
    {
        $this->cat = $this->unpackModel('cat');
        $this->pivot = $this->unpackModel('pivot');

        $this->instance_($this->pivot->id);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $ss = ss();

        $cat = $this->cat;
        $pivot = $this->pivot;

        $globalEditable = $ss->globalEditable();
        $catEditable = $ss->cats->isEditable($cat);

        $pivotData = _j($pivot->data);
        $imageData = ap($pivotData, 'item');

        $images = \ss\components\images\cat($cat)->imagesBuilder()->where('enabled', true)->orderBy('position')->get();

        foreach ($images as $image) {
            $imageData['model'] = $image;
            $imageData['pivot'] = $pivot;

            $v->assign('image', [
                'ID'      => $image->id,
                'CONTENT' => $this->c('>image:view', $imageData)
            ]);

            if ($globalEditable && $catEditable) {
                $v->assign('image/cp');

                $v->append('image', [
                    'PAGE_DIALOG_BUTTON' => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:imageDialog',
                        'data'  => [
                            'image' => xpack_model($image)
                        ],
                        'class' => 'image_dialog button',
                        'icon'  => 'fa fa-cog'
                    ])
                ]);

                $v->assign('image/not_published_mark', [
                    'HIDDEN_CLASS' => $image->published ? 'hidden' : ''
                ]);
            }
        }

        if ($globalEditable && $catEditable) {
            $this->c('\std\ui sortable:bind', [
                'selector'       => $this->_selector('|') . ' > .images',
                'path'           => '>xhr:arrange',
                'items_id_attr'  => 'image_id',
                'data'           => [
                    'cat' => xpack_model($cat)
                ],
                'plugin_options' => [
                    'distance' => 15
                ]
            ]);
        }

        $this->css();

        $jsLinkBlocks = [];

        if (ap($imageData, 'link/enabled')) {
            $htmlLinkTarget = ap($imageData, 'link/html_link_target');

            foreach (l2a('block, image, header, text') as $block) {
                if ($htmlLinkTarget != $block && ap($imageData, $block . '/js_link')) {
                    $jsLinkBlocks[] = $block;
                }
            }
        }

        $this->widget(':|', [
            '.e'              => [
//                'ss/container/' . $cat->id . '/update_pivot' => 'mr.reload'
            ],
            '.r'              => [
//                'reload'        => $this->_abs('>xhr:reload', [
//                    'cat'   => xpack_model($cat),
//                    'pivot' => xpack_model($pivot)
//                ]),
'reloadImage' => $this->_abs('>xhr:reloadImage', [
    'cat'   => xpack_model($cat),
    'pivot' => xpack_model($pivot)
])
            ],
            'catId'           => $cat->id,
            'pivotId'         => $pivot->id,
            'imageSelector' => $this->_selector('>image:'),
            'jsLinkBlocks'    => $jsLinkBlocks
        ]);

        $this->c('\plugins\cssElementQueries~:load');

        return $v;
    }
}
