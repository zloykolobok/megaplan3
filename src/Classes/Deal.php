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

    public function addComment($id,$content, $attaches)
    {
        $data = [
            'contentType' => 'Comment',
            'content' => $content,
        ];
        $method = 'POST';
        $action = '/api/v3/deal/'.$id.'/comments';

        $res = $this->send($data, $action, $method);

        return $res;
    }
}
