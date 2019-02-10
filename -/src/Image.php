<?php namespace ss\components\images;

class Image
{
    private $image;

    public function __construct(\ss\components\images\models\Image $image)
    {
        $this->image = $image;
    }

    public function duplicate()
    {
        $newImage = \ss\components\images\models\Image::create($this->image->toArray());

        appc('\std\images~:copy', [
            'source' => $this->image,
            'target' => $newImage
        ]);

        return $newImage;
    }

    public function delete()
    {
        $this->image->delete();
    }

    public function linkData($path = false, $value = null)
    {
        $data = _j($this->image->link_data);

        if (null === $value) {
            return ap($data, $path);
        } else {
            ap($data, $path, $value);

            $this->image->link_data = j_($data);
            $this->image->save();
        }
    }

    public function reset()
    {
        $this->resetCache();
        $this->resetImages();
    }

    public function resetCache()
    {

    }

    public function resetImages()
    {
        $this->removeNotOriginalImageVersions();

        $this->image->update(['images_cache' => '']);
    }

    public function removeNotOriginalImageVersions()
    {
        $query = \std\images\Support::normalizeQuery();

        $images = $this->image->images;

        $imagesIds = table_ids($images);

        $notOriginalVersions = \std\images\models\Version::whereIn('image_id', $imagesIds)->where('query', '!=', $query)->get();

        $deleted = 0;
        foreach ($notOriginalVersions as $version) {
            $version->delete();

            if (appc('\std\images~')->tryUnlink($version)) {
                $deleted++;
            }
        }

        return $deleted;
    }
}
