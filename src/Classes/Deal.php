<?php

namespace Zloykolobok\Megaplan3\Classes;

use Zloykolobok\Megaplan3\Megaplan;

class Deal extends Megaplan
{
    public function getHistoryStatus($dealId)
    {
        $method = 'GET';
        $action = '/api/v3/deal/'.$dealId.'/statusHistory';
        $data = [];

        $res = $this->send($data, $action, $method);

        return $res;
    }
}
