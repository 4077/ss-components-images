<?php namespace ss\components\images\cp\set\controllers\main\image;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($image = $this->unxpackModel('image')) {
            $this->c('<:reload', [
                'image' => $image
            ]);
        }
    }

    public function toggleEnabled()
    {
        if ($image = $this->unxpackModel('image')) {
            $image->enabled = !$image->enabled;
            $image->save();

            pusher()->trigger('ss/components/images/update', [
                'catId' => $image->target_id
            ]);

            pusher()->trigger('ss/components/images/image/toggle_enabled', [
                'id' => $image->id
            ]);
        }
    }

    public function duplicate()
    {
        if ($image = $this->unxpackModel('image')) {
            \ss\components\images\image($image)->duplicate();

            pusher()->trigger('ss/components/images/update', [
                'catId' => $image->target_id
            ]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/components/images');
        } else {
            if ($image = $this->unxpackModel('image')) {
                if ($this->data('confirmed')) {
                    \ss\components\images\image($image)->delete();

                    pusher()->trigger('ss/components/images/update', [
                        'catId' => $image->target_id
                    ]);

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/components/images');
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|ss/components/images', [
                        'path' => '\std dialogs/confirm~:view',
                        'data' => [
                            'confirm_call' => $this->_abs(':delete', ['image' => xpack_model($image)]),
                            'discard_call' => $this->_abs(':delete', ['image' => xpack_model($image)]),
                            'message'      => 'Удалить <b>' . ($image->header ? $image->header : '...') . '</b>?'
                        ],
                        'ui'   => [
                            'resizable' => 'false'
                        ]
                    ]);
                }
            }
        }
    }

    public function toggleLinkEnabled()
    {
        if ($image = $this->unxpackModel('image')) {
            $image->link_enabled = !$image->link_enabled;
            $image->save();

            pusher()->trigger('ss/components/images/image/update', [
                'id' => $image->id
            ]);
        }
    }

    public function setLinkMode()
    {
        if ($image = $this->unxpackModel('image')) {
            \ss\components\images\image($image)->linkData('mode', $this->data('value'));

            pusher()->trigger('ss/components/images/image/update', [
                'id' => $image->id
            ]);
        }
    }

    public function updateHeader()
    {
        if ($image = $this->unxpackModel('image')) {
            $txt = \std\ui\Txt::value($this);

            $image->header = $txt->value;
            $image->save();

            $txt->response();

            pusher()->trigger('ss/components/images/image/update', [
                'id' => $image->id
            ]);
        }
    }

    public function updateLinkValue()
    {
        if ($image = $this->unxpackModel('image')) {
            $txt = \std\ui\Txt::value($this);

            $linkMode = \ss\components\images\image($image)->linkData('mode');

            \ss\components\images\image($image)->linkData('value/' . $linkMode, $txt->value);

            $txt->response();

            pusher()->trigger('ss/components/images/image/update', [
                'id' => $image->id
            ]);
        }
    }

    public function updateText()
    {
        if ($image = $this->unxpackModel('image')) {
            $image->text = $this->data('value');
            $image->save();

            pusher()->trigger('ss/components/images/image/update', [
                'id' => $image->id
            ]);
        }
    }

    public function imagesDialog()
    {
        if ($image = $this->unxpackModel('image')) {
            $this->c('\std\ui\dialogs~:open:imageImages, ss|ss/components/images', [
                'path'  => '\std\images\ui~:view|ss/components/images',
                'data'  => [
                    'imageable' => pack_model($image),
                    'instance'  => $this->data('instance'),
                    'href'      => [
                        'enabled' => true
                    ],
                    'callbacks' => [
                        'update' => $this->_abs('@app:imagesUpdateCallback', [
                            'image' => pack_model($image)
                        ])
                    ]
                ],
                'class' => 'padding'
            ]);

//            $this->e('images/delete', ['image_id' => $image->id])->rebind('\std\ui\dialogs~:close:imageImages|images');
        }
    }
}
