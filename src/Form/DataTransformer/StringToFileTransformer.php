<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Transform the File object to a string if needed
        return $value;
    }

    public function reverseTransform($value)
    {
        // Transform the string to a File object
        return new File($value);
    }
}
