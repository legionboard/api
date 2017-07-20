<?php

namespace LegionBoard\tests;

class Utilities
{

    public static function getMockFile()
    {
        $file = 'k=1234567890abcdef&teacher=123';
        return $file;
    }

    public static function getMockRequest()
    {
        $request = '/changes';
        return $request;
    }
}
