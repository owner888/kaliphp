<?php
namespace app\event;
use kaliphp\log;


class test_event
{
    public function test($event)
    {
        log::info("tigger in event".$event);
    }
}
