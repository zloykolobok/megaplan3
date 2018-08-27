<?php

namespace Zloykolobok\Megaplan3\Classes;

use Zloykolobok\Megaplan3\Megaplan;

class Contractor extends Megaplan
{
    /**
     * Получаем список полей для клиента-человек
     *
     * @return void
     */
    public function getContractorHumanFields()
    {
        $method = 'GET';
        $action = '/api/v3/contractorHuman/extraFields/';
        $data = [];

        $res = $this->send($data, $action, $method);

        return $res;
    }

     /**
     * Получаем список полей для клиента-компании
     *
     * @return void
     */
    public function getContractorCompanyFields()
    {
        $method = 'GET';
        $action = '/api/v3/contractorCompany/extraFields/';
        $data = [];

        $res = $this->send($data, $action, $method);

        return $res;
    }

    /**
     * Получаем список клиентов-людей
     *
     * @param [type] $pagination - пагинация
     * ['limit' => 100, 'pageAfter' => ['contentType'=>'Employee', 'id'=>'100']]
     * @return void
     */
    public function getContractorsHuman($pagination)
    {
        $method = 'GET';
        $action = '/api/v3/contractorHuman/';
        $data = $pagination;

        $res = $this->send($data,$action,$method);
        return $res;
    }

    /**
     * Получаем список клиентов-компаний
     *
     * @param [type] $pagination - пагинация
     * ['limit' => 100, 'pageAfter' => ['contentType'=>'Employee', 'id'=>'100']]
     * @return void
     */
    public function getContractorsCompany($pagination)
    {
        $method = 'GET';
        $action = '/api/v3/contractorCompany/';
        $data = $pagination;

        $res = $this->send($data,$action,$method);
        return $res;
    }

    /**
     * Получаем клиента-человека по ID
     *
     * @param [type] $id
     * @return void
     */
    public function getContractorHuman($id)
    {
        $method = 'GET';
        $action = '/api/v3/contractorHuman/'.$id;
        $data = [];

        $res = $this->send($data, $action, $method);

        return $res;
    }

    /**
     * Получаем клиента-компанию по ID
     *
     * @param [type] $id
     * @return void
     */
    public function getContractorCompany($id)
    {
        $method = 'GET';
        $action = '/api/v3/contractorCompany/'.$id;
        $data = [];

        $res = $this->send($data, $action, $method);

        return $res;
    }

    /**
     * Редактирование клиент-человека
     *
     * @param [type] $id
     * @param [type] $data
     * @return void
     */
    public function editContractorHuman($id, $data)
    {
        $method = 'POST';
        $action = '/api/v3/contractorHuman/'.$id;

        $res = $this->send($data, $action, $method);

        return $res;
    }

    /**
     * Редактирование клиент-компанию
     *
     * @param [type] $id
     * @param [type] $data
     * @return void
     */
    public function editContractorCompany($id, $data)
    {
        $method = 'POST';
        $action = '/api/v3/contractorHuman/'.$id;

        $res = $this->send($data, $action, $method);

        return $res;
    }

    /**
     * Добавление комментария для клиент-человек
     *
     * @param [type] $id - ID клиента
     * @param [type] $content - текст комментария
     * @return void
     */
    public function addCommentContractorHuman($id,$content, $attaches)
    {
        $data = [
            'contentType' => 'Comment',
            'content' => $content,
        ];
        $method = 'POST';
        $action = '/api/v3/contractorHuman/'.$id.'/comments';

        $res = $this->send($data, $action, $method);

        return $res;
    }

    /**
     * Добавление комментария для клиент-компания
     *
     * @param [type] $id - ID клиента
     * @param [type] $content - текст комментария
     * @return void
     */
    public function addCommentContractorCompany($id,$content)
    {
        $data = [
            'contentType' => 'Comment',
            'content' => $content,
        ];
        $method = 'POST';
        $action = '/api/v3/contractorHuman/'.$id.'/comments';

        $res = $this->send($data, $action, $method);

        return $res;
    }
}