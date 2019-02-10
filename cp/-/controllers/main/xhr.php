<?php namespace ss\components\images\cp\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private function triggerUpdate($catId)
    {
        pusher()->trigger('ss/container/' . $catId . '/update_pivot');
    }

    public function reload()
    {
        $this->c('<:reload', [], true);
    }

    public function setHtmlLinkTarget()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $target = $this->data('target');

            $currentTarget = ss()->cats->apComponentPivotData($pivot, 'image/link/html_link_target');

            if ($currentTarget == $target) {
                ss()->cats->apComponentPivotData($pivot, 'image/link/html_link_target', false);
            } else {
                ss()->cats->apComponentPivotData($pivot, 'image/link/html_link_target', $target);
            }

            $targetJsLink = ss()->cats->apComponentPivotData($pivot, 'image/' . $target . '/js_link');

            if ($targetJsLink) {
                ss()->cats->apComponentPivotData($pivot, 'image/' . $target . '/js_link', false);
            }

            $this->triggerUpdate($pivot->cat_id);
        }
    }

    public function toggleJsLink()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $type = $this->data('type');

            $htmlLinkTarget = ss()->cats->apComponentPivotData($pivot, 'image/link/html_link_target');

            ss()->cats->invertComponentPivotData($pivot, 'image/' . $type . '/js_link');

            if ($htmlLinkTarget == $type) {
                ss()->cats->apComponentPivotData($pivot, 'image/link/html_link_target', false);
            }

            $this->triggerUpdate($pivot->cat_id);
        }
    }

    public function toggleLinkEnabled()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            ss()->cats->invertComponentPivotData($pivot, 'image/link/enabled');

            $this->triggerUpdate($pivot->cat_id);
        }
    }

    public function toggleEnabled()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $type = $this->data('type');

            ss()->cats->apComponentPivotData($pivot, 'image/' . $type . '/enabled', $this->data('value'));

            $this->triggerUpdate($pivot->cat_id);
        }
    }

    public function arrange()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            if ($sequence = $this->data('sequence')) {
                ss()->cats->apComponentPivotData($pivot, 'image/order', implode($sequence));

                $this->triggerUpdate($pivot->cat_id);
            }
        }
    }
}
