<?php
namespace App\Libraries\Gst;

class Formatter20 implements FormatterInterface
{
    public function format(array $invoice): array
    {
        // Structure  matches 2.0 spec
        return $invoice;
    }
}