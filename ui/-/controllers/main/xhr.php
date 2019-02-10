<?php namespace ss\components\images\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload', [], true);
    }

    public function reloadImage()
    {
        if ($image = \ss\components\images\models\Image::find($this->data('id'))) {
            if ($pivot = $this->unxpackModel('pivot')) {
                $pivotData = _j($pivot->data);

                $imageData = ap($pivotData, 'item');
                $imageData['model'] = $image;
                $imageData['pivot'] = $pivot;

                $this->c('@image:reload', $imageData);

                $this->widget('~:|' . $pivot->id, 'bindJsLinkBlocks');
            }
        }
    }

    public function arrange()
    {
        foreach ((array)$this->data('sequence') as $n => $id) {
            if ($model = \ss\components\images\models\Image::find($id)) {
                $model->position = (int)$n * 10;
                $model->save();
            }
        }

        if (!empty($model)) {
            pusher()->trigger('ss/components/images/update', [
                'catId' => $model->target_id
            ]);
        }
    }

    public function imageDialog()
    {
        if ($image = $this->unxpackModel('image')) {
//            if (ss()->products->isEditable($product)) {
            $this->c('\std\ui\dialogs~:open:imageEditor, ss|ss/cats', [
                'path'          => '\ss\components\images\cp\set~image:view|ss/cats',
                'data'          => [
                    'image' => pack_model($image)
                ],
                'class'         => 'padding',
                'pluginOptions' => [
                    'title' => $image->header
                ]
            ]);
//            }
        }
    }
}
