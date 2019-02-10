<?php namespace ss\components\images;

class Cat
{
    private $cat;

    public function __construct(\ss\models\Cat $cat)
    {
        $this->cat = $cat;
    }

    public function imagesBuilder()
    {
        return $this->cat->morphMany(\ss\components\images\models\Image::class, 'target');
    }

    public function reset()
    {
        $this->resetImagesCache();
        $this->resetImagesImages();
    }

    public function resetImagesCache()
    {
        $images = $this->imagesBuilder()->get();

        foreach ($images as $image) {
            image($image)->resetCache();
        }
    }

    public function resetImagesImages()
    {
        $images = $this->imagesBuilder()->get();

        foreach ($images as $image) {
            image($image)->resetImages();
        }
    }
}
