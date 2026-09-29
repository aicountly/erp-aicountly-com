<?php
namespace App\Libraries\Gst;

use InvalidArgumentException;
use App\Libraries\Gst\Formatter10;//1.0
use App\Libraries\Gst\Formatter20; //2.0
use App\Libraries\Gst\Formatter31; // 3.1
use App\Libraries\Gst\Formatter41; //4.1
class FormatterFactory
{
    /**
     * Return the proper Formatter for a given version string.
     */
    public static function make(string $version): FormatterInterface
    {
        return match ($version) {
            '4.1'   => new Formatter41(),
			'2.0'   => new Formatter20(),
			'1.0'   => new Formatter10(),
            '3.1',  => new Formatter31(),
            default => new Formatter31(),
        };
    }
}
