<?php

declare(strict_types=1);

class CTypeFormatter extends CFormatter
{
    public function format($value,$type)
    {
        if (is_string($value) && false !== strpos($value, '<pre>&lt;?xml')) {
            $type = "raw";
        }
        return parent::format($value, $type);
    }
}
