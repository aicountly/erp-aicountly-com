<?php
namespace App\Libraries\Gst;

interface FormatterInterface
{
    /**
     * Tweak / reshape an invoice row so it matches a
     * specific API-version schema.
     *
     * @param array $invoice
     * @return array
     */
    public function format(array $invoice,string $section_type): array;
}