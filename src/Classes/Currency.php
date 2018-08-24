<?php

namespace Zloykolobok\Megaplan3\Classes;

use Zloykolobok\Megaplan3\Megaplan;

class Currency extends Megaplan
{
    /**
     * Получаем список валют
     *
     * @param array $data
     * @return void
     */
    public function getCurrency(array $data=null)
    {
        $data['access_token'] = $this->access_token;
        $method = 'GET';
        $action = '/api/v3/currency';

        $res = $this->send($data,$action,$method);

        return $res;
    }
}