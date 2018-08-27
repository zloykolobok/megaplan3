<?php

namespace Zloykolobok\Megaplan3\Classes;

use Zloykolobok\Megaplan3\Megaplan;

class Program extends Megaplan
{
    public function getPrograms()
    {
        $method = 'GET';
        $action = '/api/v3/program';
        $data = [];

        $res = $this->send($data,$action,$method);

        return $res;
    }

    public function getProgram($id)
    {
        $method = 'GET';
        $action = '/api/v3/program/'.$id;
        $data = [];

        $res = $this->send($data,$action,$method);

        return $res;
    }
}