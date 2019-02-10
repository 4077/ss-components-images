<?php namespace ss\components\images\ui\controllers\main;

class Image extends \Controller
{
    private $image;

    private $linkEnabled;

    private $linkTarget;

    public function __create()
    {
        if ($this->image = $this->unpackModel()) {
            $this->instance_($this->image->id);

            $this->linkEnabled = $this->image->link_enabled && $this->data('link/enabled') ?? false;
            $this->linkTarget = $this->data('link/html_link_target') ?? false;
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

        $order = $this->data('order');

        for ($i = 0; $i < strlen($order); $i++) {
            $blockIndex = $order[$i];

            if ($blockIndex == 'i') {
                $this->assignImage($v);
            }

            if ($blockIndex == 'h') {
                $this->assignHeader($v);
            }

            if ($blockIndex == 't') {
                $this->assignText($v);
            }
        }

        list($v, $css) = $this->cssVars($v);

        $this->css($css['path'] . '|' . $css['vmd5'])->setVars($css['vars']);

        $v->assign([
                       'CLASS' => $this->data('class')
                   ]);

        if ($this->linkEnabled) {
            if ($this->linkTarget == 'block') {
                $v = $this->linkWrap($v);
            } else {
                if ($this->data('block/js_link')) {
                    $this->addJsLink('block');
                }
            }
        }

        if ($this->jsLinkBlocks) {
            $imageHelper = \ss\components\images\image($this->image);

            $linkMode = $imageHelper->linkData('mode');

            $this->jquery('|')->data([
                                         'link' => [
                                             'mode'  => $linkMode,
                                             'value' => $this->renderLink()
                                         ]
                                     ]);
        }

        return $v;
    }

    private $jsLinkBlocks = [];

    private function addJsLink($block)
    {
        $this->jsLinkBlocks[] = $block;
    }

    private function assignImage(\ewma\Views\View $v)
    {
        if ($this->data('image/enabled')) {
            if ($this->data('image/original')) {
                $query = false;
            } else {
                $width = $this->data('image/auto_width') ? '-' : $this->data('image/width');
                $height = $this->data('image/auto_height') ? '-' : $this->data('image/height');

                $query = $width . ' ' . $height . ' ' . ($this->data('image/resize_mode')) . ($this->data('image/prevent_upsize') ? ' preventUpsize' : '');
            }

            $class = $this->data('image/class') or
            $class = 'image';

            $image = $this->c('\std\images~:first', [
                'model'       => $this->image,
                'query'       => $query,
                'cache_field' => 'images_cache'
            ]);

            if ($image) {
                $versionModel = $image->versionModel;

                $content = '<img src="' . abs_url($versionModel->file_path) . '" width="' . $versionModel->width . '" height="' . $versionModel->height . '" />';

                $content = $this->c('\std\ui tag:view', [
                    'attrs'   => [
                        'class' => $class,
                        'block' => 'image'
                    ],
                    'content' => $content
                ]);

                if (!$this->linkEnabled || !in($this->linkTarget, 'block, image')) {
                    $hrefData = $this->data('image/href');

                    if ($hrefData['enabled']) {
                        if ($this->data('image/href/original')) {
                            $query = false;
                        } else {
                            $width = $this->data('image/href/auto_width') ? '-' : $this->data('image/href/width');
                            $height = $this->data('image/href/auto_height') ? '-' : $this->data('image/href/height');

                            $query = $width . ' ' . $height . ' ' . ($this->data('image/href/resize_mode')) . ($this->data('image/href/prevent_upsize') ? ' preventUpsize' : '');
                        }

                        $image = $this->c('\std\images~:first', [
                            'model'       => $this->image,
                            'query'       => $query,
                            'cache_field' => 'images_cache'
                        ]);

                        if ($image) {
                            $content = $this->c('\std\ui tag:view:a', [
                                'attrs'   => [
                                    'href' => abs_url($image->versionModel->file_path)
                                ],
                                'content' => $content
                            ]);
                        }
                    }
                }

                if ($this->linkEnabled) {
                    if ($this->linkTarget == 'image') {
                        $content = $this->linkWrap($content);
                    } else {
                        if ($this->data('image/js_link')) {
                            $this->addJsLink('image');
                        }
                    }
                }

                $v->assign('block', [
                    'CONTENT' => $content
                ]);
            }
        }
    }

    private function assignHeader(\ewma\Views\View $v)
    {
        if ($this->data('header/enabled')) {
            $tagName = $this->data('header/tag');

            $class = $this->data('header/class') or
            $class = 'header';

            $content = $this->c('\std\ui tag:view:' . $tagName, [
                'attrs'   => [
                    'class' => $class,
                    'block' => 'header'
                ],
                'content' => $this->image->header
            ]);

            if ($this->linkEnabled) {
                if ($this->linkTarget == 'header') {
                    $content = $this->linkWrap($content);
                } else {
                    if ($this->data('header/js_link')) {
                        $this->addJsLink('header');
                    }
                }
            }

            $v->assign('block', [
                'CONTENT' => $content
            ]);
        }
    }

    private function assignText(\ewma\Views\View $v)
    {
        if ($this->data('text/enabled')) {
            $tagName = $this->data('text/tag');

            $class = $this->data('text/class') or
            $class = 'text';

            $content = $this->c('\std\ui tag:view:' . $tagName, [
                'attrs'   => [
                    'class' => $class,
                    'block' => 'text'
                ],
                'content' => $this->image->text
            ]);

            if ($this->linkEnabled) {
                if ($this->linkTarget == 'text') {
                    $content = $this->linkWrap($content);
                } else {
                    if ($this->data('text/js_link')) {
                        $this->addJsLink('text');
                    }
                }
            }

            $v->assign('block', [
                'CONTENT' => $content
            ]);
        }
    }

    private function renderLink()
    {
        $imageHelper = \ss\components\images\image($this->image);

        $linkMode = $imageHelper->linkData('mode');
        $linkValue = $imageHelper->linkData('value/' . $linkMode);

        $link = false;

        if ($linkMode == 'cat') {
            if ($cat = \ss\models\Cat::find($linkValue)) {
                if ($cat->type == 'container') {
                    if ($parent = $cat->parent) {
                        $link = abs_url($parent->route_cache, '#' . $cat->id);
                    }
                } else {
                    $link = abs_url($cat->route_cache);
                }
            }
        }

        if ($linkMode == 'route') {
            $link = abs_url($linkValue);
        }

        if ($linkMode == 'anchor') {
            $link = '#' . $linkValue;
        }

        if ($linkMode == 'url') {
            $link = $linkValue;
        }

        return $link;
    }

    private function linkWrap($content)
    {
        if ($link = $this->renderLink()) {
            $content = $this->c('\std\ui tag:view:a', [
                'attrs'   => [
                    'href' => $link
                ],
                'content' => $content
            ]);
        }

        return $content;
    }

    private function cssVars(\ewma\Views\View $v)
    {
        $css = $this->data('css') or
        $css = ':\css\std~';

        $width = $this->data('image/auto_width') ? 'auto' : (int)$this->data('image/width') . 'px';
        $height = $this->data('image/auto_height') ? 'auto' : (int)$this->data('image/height') . 'px';

        $cssVars = [
            'imageWidth'  => $width,
            'imageHeight' => $height
        ];

        $cssVarsMd5 = '_' . jmd5($cssVars);

        $cssVars['vmd5'] = $cssVarsMd5;

        $v->assign('VMD5', $cssVarsMd5);

        $cssData = [
            'path' => $css,
            'vmd5' => $cssVarsMd5,
            'vars' => $cssVars
        ];

        return [$v, $cssData];
    }
}
