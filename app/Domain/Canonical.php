<?php

namespace App\Domain;

final class Canonical
{
    public static function value(mixed $v): mixed
    {
        if (! is_array($v)) {
            return $v;
        }if (! array_is_list($v)) {
            ksort($v);
        }foreach ($v as &$item) {
            $item = self::value($item);
        }

return $v;
    }

    public static function same(mixed $a, mixed $b): bool
    {
        return self::value($a) === self::value($b);
    }
}
