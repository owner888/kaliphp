<?php

namespace kali;

class Test
{
    public function __construct()
    {
        echo __method__."\n";
    }

    public function get()
    {
        echo __method__."\n";
        echo "get\n";
    }
}
