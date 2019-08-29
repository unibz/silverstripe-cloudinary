<?php

namespace MadeHQ\Cloudinary\FieldType;

use MadeHQ\Cloudinary\Forms\ImageField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use BadFunctionCallException;

class DBImage extends DBSingle
{
    /**
     * @config
     */
    private static $non_gravity_crops = ['fit', 'limit', 'mfit', 'pad', 'lpad'];

    protected $defaultTransformationsKey = 'default_image_transformations';

    public function Size(/* int */ $width, /* int */ $height)
    {
        return $this->AddTransformations([
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function Width(/* int */ $width)
    {
        return $this->AddTransformation('width', $width);
    }

    public function Height(/* int */ $height)
    {
        return $this->AddTransformation('height', $height);
    }

    public function ResizeByWidth(/* int */ $width, $crop = 'scale')
    {
        return $this
            ->RemoveTransformation('height')
            ->AddTransformation('width', $width)
            ->AddTransformation('crop', $crop);
    }

    public function ResizeByHeight(/* int */ $height, $crop = 'scale')
    {
        return $this
            ->RemoveTransformation('width')
            ->AddTransformation('height', $height)
            ->AddTransformation('crop', $crop);
    }

    public function Crop($crop = 'scale')
    {
        return $this->AddTransformation('crop', $crop);
    }

    public function Gravity($gravity = 'auto')
    {
        return $this->AddTransformation('gravity', $gravity);
    }

    public function FetchFormat($fetchFormat = 'auto')
    {
        return $this->AddTransformation('fetch_format', $fetchFormat);
    }

    public function Quality($quality = 'auto')
    {
        return $this->AddTransformation('quality', $quality);
    }

    public function Radius(...$args)
    {
        if (empty($args)) {
            throw new BadFunctionCallException('Please provide rounding value(s)');
        }

        if (count($args) > 1) {
            $radius = implode(':', $args);
        } else {
            $radius = reset($args);
        }

        if ($radius === '0' || $radius === 0) {
            return $this->RemoveTransformation('radius');
        }

        return $this->AddTransformation('radius', $radius);
    }

    public function Rotate(/* string */ $rotate)
    {
        return $this->AddTransformation('rotate', $rotate);
    }

    public function TopColours()
    {
        $colours = $this->getJSONValue('top_colours');

        if (!$colours) {
            return null;
        }

        $return = ArrayList::create();

        foreach ($colours as $colour) {
            $return->push(ArrayData::create([
                'Colour' => $colour->colour,
                'Prominence' => $colour->prominence,
            ]));
        }

        return $return;
    }

    protected function parseTransformations(array &$transformations)
    {
        $nonGravityCrops = static::config()->get('non_gravity_crops');

        foreach ($transformations as &$transformation) {
            $cropExists = array_key_exists('crop', $transformation);
            $gravityExists = array_key_exists('gravity', $transformation);

            if ($cropExists === false && $gravityExists === true) {
                unset($transformation['gravity']);
            }

            if ($cropExists === true && in_array($transformation['crop'], $nonGravityCrops) === true) {
                unset($transformation['gravity']);
            }
        }
    }

    public function scaffoldFormField($title = null, $params = null)
    {
        return ImageField::create($this->name, $title);
    }
}