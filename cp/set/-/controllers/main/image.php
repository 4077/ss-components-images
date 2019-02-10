<?php namespace ss\components\images\cp\set\controllers\main;

class Image extends \Controller
{
    private $image;

    private $cat;

    private $alone;

    public function __create()
    {
        if ($this->image = $this->unpackModel('image')) {
            $this->instance_($this->image->id);

            $this->alone = !$this->data('~');

            $this->cat = $this->data('cat') or
            $this->cat = $this->image->target;
        } else {
            $this->lock();
        }
    }

    // todo перезагрузка когда открыта без списка

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $image = $this->image;
        $imageXPack = xpack_model($image);

        $linkEnabled = $image->link_enabled;
        $linkData = \ss\components\images\image($image)->linkData();
        $linkMode = ap($linkData, 'mode');

        $v->assign([
                       'CACHE_SIZE'            => trim_zeros(number_format__(strlen($image->images_cache))) . ' b',
                       'ID'                    => $image->id,
                       'ENABLED_CLASS'         => $image->enabled ? 'enabled' : '',
                       'IMAGE'                 => $this->imageView($image),
                       'LINK_ENABLED_BUTTON'   => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:toggleLinkEnabled',
                           'data'  => [
                               'image' => $imageXPack
                           ],
                           'class' => 'link_enabled_button ' . ($linkEnabled ? 'enabled' : ''),
                           'title' => $linkEnabled ? 'Выключить' : 'Включить',
                           'icon'  => 'fa fa-power-off',
                           'label' => 'Ссылка'
                       ]),
                       'LINK_MODE_SWITCHER'    => $linkEnabled
                           ? $this->c('\std\ui\switcher~:view', [
                               'path'    => $this->_p('>xhr:setLinkMode'),
                               'data'    => [
                                   'image' => $imageXPack,
                               ],
                               'value'   => $linkMode,
                               'class'   => 'link_mode_switcher',
                               'classes' => [
                                   'selected' => 'selected'
                               ],
                               'buttons' => [
                                   [
                                       'value' => 'cat',
                                       'icon'  => 'fa fa-tree',
                                       'class' => 'cat',
                                       'title' => 'Ссылка на страницу, контейнер или папку'
                                   ],
                                   [
                                       'value' => 'route',
                                       'icon'  => 'fa fa-link',
                                       'class' => 'route',
                                       'title' => 'Внутренняя ссылка'
                                   ],
                                   [
                                       'value' => 'anchor',
                                       'icon'  => 'fa fa-anchor',
                                       'class' => 'anchor',
                                       'title' => 'Якорь'
                                   ],
                                   [
                                       'value' => 'url',
                                       'icon'  => 'fa fa-external-link',
                                       'class' => 'url',
                                       'title' => 'Внешняя ссылка'
                                   ],
                               ]
                           ])
                           : '',
                       'HEADER'                => $this->c('\std\ui txt:view', [
                           'path'              => '>xhr:updateHeader',
                           'data'              => [
                               'image' => $imageXPack
                           ],
                           'class'             => 'txt',
                           'fitInputToClosest' => '.header',
                           'placeholder'       => 'Заголовок',
                           'content'           => $image->header
                       ]),
                       'TEXT'                  => $this->c('\std\ui\mce~:view|images/' . $image->id, [
                           'path'    => '>xhr:updateText|images/' . $image->id,
                           'data'    => [
                               'image' => $imageXPack
                           ],
                           'content' => $image->text ? $image->text : '...',
                           'options' => [
                               'inline' => true
                           ]
                       ]),
                       'TOGGLE_ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:toggleEnabled',
                           'data'  => [
                               'image' => $imageXPack
                           ],
                           'class' => 'toggle_enabled button ' . ($image->enabled ? 'enabled' : ''),
                           'title' => $image->enabled ? 'Выключить' : 'Включить',
                           'icon'  => 'fa fa-power-off'
                       ]),
                       'DUPLICATE_BUTTON'      => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:duplicate',
                           'data'  => [
                               'image' => $imageXPack
                           ],
                           'class' => 'duplicate button ' . ($image->enabled ? 'enabled' : ''),
                           'title' => 'Дублировать',
                           'icon'  => 'fa fa-clone'
                       ]),
                       'DELETE_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:delete',
                           'data'  => [
                               'image' => $imageXPack
                           ],
                           'class' => 'delete button ' . ($image->enabled ? 'enabled' : ''),
                           'title' => 'Удалить',
                           'icon'  => 'fa fa-trash-o'
                       ]),
                   ]);

        if ($linkEnabled) {
//            if ($linkMode === 'cat') {
////                $linkValueContent = $this->c('\ss\controls\catSelector~:view');
//                $linkValueContent = '';
//            } else {
            $linkValueContent = $this->c('\std\ui txt:view', [
                'path'                       => '>xhr:updateLinkValue',
                'data'                       => [
                    'image' => $imageXPack,
                    'mode'    => $linkMode
                ],
                'class'                      => 'txt',
                'editTriggerClosestSelector' => '.link_value',
                'fitInputToClosest'          => '.row',
                'placeholder'                => '',
                'content'                    => \ss\components\images\image($image)->linkData('value/' . ap($linkData, 'mode'))
            ]);
//            }

            $v->assign('link_value', [
                'CONTENT' => $linkValueContent
            ]);
        }

        $this->css(':\css\std~, \js\jquery\ui icons');

        if ($this->alone) {
            $this->widget(':|', [
                '.r' => [
                    'reload' => $this->_abs('>xhr:reload', [
                        'image' => $imageXPack
                    ])
                ],
                'id' => $image->id
            ]);
        }

        return $v;
    }

    private function imageView($model, $instance = '')
    {
        $image = $this->c('\std\images~:first', [
            'model'       => $model,
            'instance'    => $instance,
            'query'       => '100 - fit',
            'cache_field' => 'images_cache'
        ]);

        return $this->c('\std\ui button:view', [
            'path'    => '>xhr:imagesDialog',
            'data'    => [
                'image'  => xpack_model($model),
                'instance' => $instance
            ],
            'class'   => $image ? 'edit_button' : 'upload_button',
            'content' => $image ? $image->view : 'Загрузить'
        ]);
    }
}
