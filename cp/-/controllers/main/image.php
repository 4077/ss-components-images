<?php namespace ss\components\images\cp\controllers\main;

class Image extends \Controller
{
    private $pivot;

    public function __create()
    {
        if ($this->pivot = $this->unpackModel('pivot')) {
            $this->instance_($this->pivot->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $pivot = $this->pivot;
        $pivotXPack = xpack_model($pivot);

        $pivotData = _j($pivot->data);

        $imageData = ap($pivotData, 'item/image');

        $originalUsed = $imageData['original'];

        $v->assign([
                       'ENABLED_CLASS'             => $originalUsed ? '' : 'enabled',
                       'TOGGLE_ORIGINAL_BUTTON'    => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleOriginal',
                           'data'    => [
                               'pivot' => $pivotXPack
                           ],
                           'class'   => 'toggle_original_button ' . ($originalUsed ? 'enabled' : ''),
                           'content' => 'Оригинал'
                       ]),
                       'AUTO_WIDTH_KNOB_CLASS'     => $imageData['auto_width'] ? 'auto' : '',
                       'W_KNOB'                    => $imageData['auto_width']
                           ? ''
                           : $this->c('\plugins\knob~:view|ss/components/images/pivot_' . $pivot->id . '/image_width', [
                               'min'            => 0,
                               'max'            => 1920,
                               'value'          => $imageData['width'],
                               'width'          => 80,
                               'height'         => 80,
                               'fgColor'        => $originalUsed ? '#d0d0d0' : '#87ceeb',
                               'update_request' => $this->_abs('>xhr:updateDimension', [
                                   'pivot'     => $pivotXPack,
                                   'dimension' => 'width'
                               ])
                           ]),
                       'TOGGLE_AUTO_WIDTH_BUTTON'  => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleDimensionAuto',
                           'data'    => [
                               'pivot'     => $pivotXPack,
                               'dimension' => 'width'
                           ],
                           'class'   => 'toggle_auto_button ' . ($imageData['auto_width'] ? 'enabled' : ''),
                           'title'   => $imageData['auto_width'] ? 'Выключить' : 'Включить',
                           'content' => 'авто'
                       ]),
                       'AUTO_HEIGHT_KNOB_CLASS'    => $imageData['auto_height'] ? 'auto' : '',
                       'H_KNOB'                    => $imageData['auto_height']
                           ? ''
                           : $this->c('\plugins\knob~:view|ss/components/images/pivot_' . $pivot->id . '/image_height', [
                               'min'            => 0,
                               'max'            => 1920,
                               'value'          => $imageData['height'],
                               'width'          => 80,
                               'height'         => 80,
                               'fgColor'        => $originalUsed ? '#d0d0d0' : '#87ceeb',
                               'update_request' => $this->_abs('>xhr:updateDimension', [
                                   'pivot'     => $pivotXPack,
                                   'dimension' => 'height'
                               ])
                           ]),
                       'TOGGLE_AUTO_HEIGHT_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleDimensionAuto',
                           'data'    => [
                               'pivot'     => $pivotXPack,
                               'dimension' => 'height'
                           ],
                           'class'   => 'toggle_auto_button ' . ($imageData['auto_height'] ? 'enabled' : ''),
                           'title'   => $imageData['auto_height'] ? 'Выключить' : 'Включить',
                           'content' => 'авто'
                       ]),
                       'RESIZE_MODE'               => ss()->cats->apComponentPivotData($pivot, 'item/image/resize_mode'),
                       'PREVENT_UPSIZE_ENABLED'    => ss()->cats->apComponentPivotData($pivot, 'item/image/prevent_upsize') ? 'enabled' : ''
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|') . ' .image_control .resize_mode',
            'path'     => '>xhr:toggleResizeMode',
            'data'     => [
                'pivot' => $pivotXPack,
            ]
        ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|') . ' .image_control .prevent_upsize',
            'path'     => '>xhr:togglePreventUpsize',
            'data'     => [
                'pivot' => $pivotXPack,
            ]
        ]);

        $linkEnabled = ap($pivotData, 'item/link/enabled');
        $htmlLinkTarget = ap($pivotData, 'item/link/html_link_target');

        if (!$linkEnabled || !in($htmlLinkTarget, 'block, image')) {
            $hrefData = ap($imageData, 'href');

            $hrefEnabled = $hrefData['enabled'];

            $v->assign('href', [
                'TOGGLE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'  => '>xhr:toggleHrefEnabled',
                    'data'  => [
                        'pivot' => $pivotXPack,
                    ],
                    'class' => 'toggle_enabled_button ' . ($hrefEnabled ? 'enabled' : ''),
                    'title' => $hrefEnabled ? 'Выключить' : 'Включить',
                    'icon'  => 'fa fa-link',
                    'label' => 'Ссылка на другой размер'
                ])
            ]);

            if ($hrefEnabled) {
                $originalUsed = $hrefData['original'];

                $v->assign('href/image_control', [
                    'ENABLED_CLASS'          => $originalUsed ? '' : 'enabled',
                    'TOGGLE_ORIGINAL_BUTTON' => $this->c('\std\ui button:view', [
                        'path'    => '>xhr:toggleHrefOriginal',
                        'data'    => [
                            'pivot' => $pivotXPack
                        ],
                        'class'   => 'toggle_original_button ' . ($originalUsed ? 'enabled' : ''),
                        'content' => 'Оригинал'
                    ]),
                    'W_KNOB'                 => $this->c('\plugins\knob~:view|ss/components/images/pivot_' . $pivot->id . '/image_href_width', [
                        'min'            => 0,
                        'max'            => 1920,
                        'value'          => $hrefData['width'],
                        'width'          => 80,
                        'height'         => 80,
                        'fgColor'        => $originalUsed ? '#d0d0d0' : '#87ceeb',
                        'update_request' => $this->_abs('>xhr:updateHrefDimension', [
                            'pivot'     => $pivotXPack,
                            'dimension' => 'width'
                        ])
                    ]),
                    'H_KNOB'                 => $this->c('\plugins\knob~:view|ss/components/images/pivot_' . $pivot->id . '/image_href_height', [
                        'min'            => 0,
                        'max'            => 1920,
                        'value'          => $hrefData['height'],
                        'width'          => 80,
                        'height'         => 80,
                        'fgColor'        => $originalUsed ? '#d0d0d0' : '#87ceeb',
                        'update_request' => $this->_abs('>xhr:updateHrefDimension', [
                            'pivot'     => $pivotXPack,
                            'dimension' => 'height'
                        ])
                    ]),
                    'RESIZE_MODE'            => ss()->cats->apComponentPivotData($pivot, 'item/image/href/resize_mode'),
                    'PREVENT_UPSIZE_ENABLED' => ss()->cats->apComponentPivotData($pivot, 'item/image/href/prevent_upsize') ? 'enabled' : ''
                ]);

                $this->c('\std\ui button:bind', [
                    'selector' => $this->_selector('|') . ' .href_image_control .resize_mode',
                    'path'     => '>xhr:toggleHrefResizeMode',
                    'data'     => [
                        'pivot' => $pivotXPack,
                    ]
                ]);

                $this->c('\std\ui button:bind', [
                    'selector' => $this->_selector('|') . ' .href_image_control .prevent_upsize',
                    'path'     => '>xhr:toggleHrefPreventUpsize',
                    'data'     => [
                        'pivot' => $pivotXPack,
                    ]
                ]);

            }
        }

        $this->css();

        return $v;
    }
}
