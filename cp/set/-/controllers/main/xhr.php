<?php namespace ss\components\images\cp\set\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('<:reload', [
                'cat' => $cat
            ]);
        }
    }

    public function reloadImage()
    {
        if ($image = \ss\components\images\models\Image::find($this->data('id'))) {
            $this->c('~image:reload', [
                'image' => $image
            ]);
        }
    }

    public function create()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $cat->otherMorphMany(\ss\components\images\models\Image::class, 'target')// todo вынести в срц
            ->create([
                         'enabled'   => true,
                         'published' => false,
                         'link_data' => j_([
                                               'mode'   => 'cat',
                                               'values' => [
                                                   'cat'    => '',
                                                   'route'  => '',
                                                   'anchor' => '',
                                                   'url'    => '',
                                               ]
                                           ])
                     ]);

            pusher()->trigger('ss/components/images/update', [
                'catId' => $cat->id
            ]);
        }
    }

    public function createFromImagesDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {

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
}
