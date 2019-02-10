<?php namespace ss\components\images\cp\set\controllers\main\image;

class App extends \Controller
{
    public function imagesUpdateCallback()
    {
        if ($image = $this->unpackModel('image')) {
            \ss\components\images\image($image)->resetImages();

            pusher()->trigger('ss/components/images/image/update', [
                'id' => $image->id
            ]);
        }
    }
}
