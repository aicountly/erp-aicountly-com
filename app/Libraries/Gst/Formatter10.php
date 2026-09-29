<?php
namespace App\Libraries\Gst;

class Formatter10 implements FormatterInterface
{
    public function format(array $invoice): array
    {
        // Structure  matches 1.0 spec
        return $invoice;
    }
}