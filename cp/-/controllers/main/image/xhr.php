<?php namespace ss\components\images\cp\controllers\main\image;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private function triggerUpdate($cat)
    {
        \ss\components\images\cat($cat)->reset();

//        pusher()->trigger('ss/components/images/update', [
//            'catId' => $cat->id
//        ]);

        pusher()->trigger('ss/container/' . $cat->id . '/update_pivot');
    }

    public function updateDimension()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $dimension = $this->data('dimension');

            if (in($dimension, 'width, height')) {
                ss()->cats->apComponentPivotData($pivot, 'image/image/' . $dimension, (int)$this->data('value'));

                $this->triggerUpdate($pivot->cat);
            }
        }
    }

    public function toggleDimensionAuto()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $dimension = $this->data('dimension');

            if (in($dimension, 'width, height')) {
                ss()->cats->invertComponentPivotData($pivot, 'image/image/auto_' . $dimension);

                $this->triggerUpdate($pivot->cat);
            }
        }
    }

    public function toggleOriginal()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $originalUsed = ss()->cats->apComponentPivotData($pivot, 'image/image/original');

            ss()->cats->apComponentPivotData($pivot, 'image/image/original', !$originalUsed);

            $this->triggerUpdate($pivot->cat);
        }
    }

    public function togglePreventUpsize()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $preventUpsize = ss()->cats->apComponentPivotData($pivot, 'image/image/prevent_upsize');

            ss()->cats->apComponentPivotData($pivot, 'image/image/prevent_upsize', !$preventUpsize);

            $this->triggerUpdate($pivot->cat);
        }
    }

    public function toggleResizeMode()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $currentMode = ss()->cats->apComponentPivotData($pivot, 'image/image/resize_mode');

            if ($currentMode == 'fill') {
                $setMode = 'fit';
            } else {
                $setMode = 'fill';
            }

            ss()->cats->apComponentPivotData($pivot, 'image/image/resize_mode', $setMode);

            $this->triggerUpdate($pivot->cat);
        }
    }

    public function toggleHrefEnabled()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $enabled = ss()->cats->apComponentPivotData($pivot, 'image/image/href/enabled');

            ss()->cats->apComponentPivotData($pivot, 'image/image/href/enabled', !$enabled);

            $this->triggerUpdate($pivot->cat);
        }
    }

    public function updateHrefDimension()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $dimension = $this->data('dimension');

            if (in($dimension, 'width, height')) {
                ss()->cats->apComponentPivotData($pivot, 'image/image/href/' . $dimension, (int)$this->data('value'));

                $this->triggerUpdate($pivot->cat);
            }
        }
    }

    public function toggleHrefOriginal()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $originalUsed = ss()->cats->apComponentPivotData($pivot, 'image/image/href/original');

            ss()->cats->apComponentPivotData($pivot, 'image/image/href/original', !$originalUsed);

            $this->triggerUpdate($pivot->cat);
        }
    }

    public function toggleHrefPreventUpsize()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $preventUpsize = ss()->cats->apComponentPivotData($pivot, 'image/image/href/prevent_upsize');

            ss()->cats->apComponentPivotData($pivot, 'image/image/href/prevent_upsize', !$preventUpsize);

            $this->triggerUpdate($pivot->cat);
        }
    }

    public function toggleHrefResizeMode()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $currentMode = ss()->cats->apComponentPivotData($pivot, 'image/image/href/resize_mode');

            if ($currentMode == 'fill') {
                $setMode = 'fit';
            } else {
                $setMode = 'fill';
            }

            ss()->cats->apComponentPivotData($pivot, 'image/image/href/resize_mode', $setMode);

            $this->triggerUpdate($pivot->cat);
        }
    }
}
