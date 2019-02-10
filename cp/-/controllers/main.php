<?php namespace ss\components\images\cp\controllers;

class Main extends \Controller
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

        $imageData = ap($pivotData, 'item');
        $linkData = ap($imageData, 'link');

        $linkEnabled = ap($linkData, 'enabled');
        $htmlLinkTarget = ap($linkData, 'html_link_target');

        $blockJsLink = ap($imageData, 'block/js_link');

        $v->assign([
                       'LINK_ENABLED_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleLinkEnabled',
                           'data'    => [
                               'pivot' => $pivotXPack
                           ],
                           'class'   => 'link_enabled_button ' . ($linkEnabled ? 'enabled' : ''),
                           'title'   => $linkEnabled ? 'Выключить' : 'Включить',
                           'icon'    => 'fa fa-link',
                           'content' => 'Ссылка'
                       ]),
                       'SET_HTML_LINK_TARGET_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => $linkEnabled,
                           'path'    => '>xhr:setHtmlLinkTarget',
                           'data'    => [
                               'pivot'  => $pivotXPack,
                               'target' => 'block'
                           ],
                           'class'   => 'set_html_link_target_button ' . ($htmlLinkTarget == 'block' ? 'selected' : ''),
                           'title'   => 'Обычная',
                           'content' => '&lt;a&gt;',
                       ]),
                       'TOGGLE_JS_LINK_BUTTON'       => $this->c('\std\ui button:view', [
                           'visible' => $linkEnabled,
                           'path'    => '>xhr:toggleJsLink',
                           'data'    => [
                               'pivot' => $pivotXPack,
                               'type'  => 'block'
                           ],
                           'class'   => 'toggle_js_link_button ' . ($blockJsLink ? 'selected' : ''),
                           'title'   => 'Программная',
                           'content' => '.href()',
                       ]),
                   ]);

        $blocks = map($this->getBlocks(), preg_split('//u', ap($imageData, 'order') ?? 'iht', null, PREG_SPLIT_NO_EMPTY));

        foreach ($blocks as $index => $block) {
            $blockType = $block['type'];
            $blockData = ap($imageData, $blockType);

            $v->assign('block', [
                'INDEX'                       => $index,
                'ENABLED_CLASS'               => $blockData['enabled'] ? 'enabled' : '',
                'TOGGLE_ENABLED_BUTTON'       => $this->c('\std\ui button:view', [
                    'path'  => '>xhr:toggleEnabled',
                    'data'  => [
                        'pivot' => $pivotXPack,
                        'type'  => $block['type'],
                        'value' => !$blockData['enabled']
                    ],
                    'class' => 'toggle_enabled_button ' . ($blockData['enabled'] ? 'enabled' : ''),
                    'title' => $blockData['enabled'] ? 'Выключить' : 'Включить',
                    'icon'  => 'fa fa-power-off'
                ]),
                'LABEL'                       => $block['label'],
                'SET_HTML_LINK_TARGET_BUTTON' => $this->c('\std\ui button:view', [
                    'visible' => $linkEnabled,
                    'path'    => '>xhr:setHtmlLinkTarget',
                    'data'    => [
                        'pivot'  => $pivotXPack,
                        'target' => $blockType
                    ],
                    'class'   => 'set_html_link_target_button ' . ($htmlLinkTarget == $blockType ? 'selected' : ''),
                    'title'   => 'Обычная',
                    'content' => '&lt;a&gt;',
                ]),
                'TOGGLE_JS_LINK_BUTTON'       => $this->c('\std\ui button:view', [
                    'visible' => $linkEnabled,
                    'path'    => '>xhr:toggleJsLink',
                    'data'    => [
                        'pivot' => $pivotXPack,
                        'type'  => $blockType
                    ],
                    'class'   => 'toggle_js_link_button ' . ($blockData['js_link'] ?? false ? 'selected' : ''),//todo
                    'title'   => 'Программная',
                    'content' => '.href()',
                ]),
                'CP'                          => $this->cpView($block['type'], $blockData)
            ]);
        }

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('|'),
            'path'           => '>xhr:arrange',
            'items_id_attr'  => 'index',
            'data'           => [
                'pivot' => $pivotXPack,
            ],
            'plugin_options' => [
                'axis'     => 'y',
                'distance' => 15,
                'handle'   => '.bar'
            ]
        ]);

        $this->css();

        $this->widget(':|', [
            '.e' => [
                'ss/container/' . $pivot->cat_id . '/update_pivot' => 'mr.reload'
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload', ['pivot' => $pivotXPack])
            ]
        ]);

        return $v;
    }

    private function cpView($blockType, $blockData)
    {
        if ($blockType == 'image') {
            return $this->c('>image:view', [
                'pivot'      => $this->pivot,
                'image_data' => $blockData
            ]);
        }
    }

    private function getBlocks()
    {
        return [
            'i' => [
                'type'  => 'image',
                'label' => 'Картинка'
            ],
            'h' => [
                'type'  => 'header',
                'label' => 'Заголовок'
            ],
            't' => [
                'type'  => 'text',
                'label' => 'Текст'
            ]
        ];
    }
}
