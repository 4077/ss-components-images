<?php namespace ss\components\images\models;

class Image extends \Model
{
    public $table = 'ss_components_images';

    public function target()
    {
        return $this->morphTo();
    }

    public function images()
    {
        return $this->morphMany(\std\images\models\Image::class, 'imageable');
    }
}

class ImageObserver
{
    public function creating($model)
    {
        $position = Image::max('position') + 10;

        $model->position = $position;
    }

    public function deleting($model)
    {
        $allowBackup = app()->rootController->__meta__->allowForCallPerform;

        app()->rootController->__meta__->allowForCallPerform = \ewma\Controllers\Controller::APP;

        app()->c('\std\images~:delete', [
            'model' => $model
        ]);

        app()->rootController->__meta__->allowForCallPerform = $allowBackup;
    }
}

Image::observe(new ImageObserver);
