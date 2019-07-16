<?php

namespace Zloykolobok\Megaplan3;

use Zloykolobok\Megaplan3\Exception\ConfigException;
use Zloykolobok\Megaplan3\Exception\SenddataException;
use Zloykolobok\Megaplan3\Exception\AuthException;
use Zloykolobok\Megaplan3\Exception\MegaplanException;
use Illuminate\Support\Facades\Storage;

class Megaplan
{

    protected $url = null;
    protected $username = null;
    protected $password = null;
    protected $header = null;
    protected $grant_type = null;
    protected $timeout = null;
    protected $session = null;

    protected $access_token;
    protected $expires_in;
    protected $token_type;
    protected $scope;
    protected $refresh_token;

    public function __construct($url,$username,$password, $timeout = 600)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->header = ['content-type: multipart/form-data'];
        $this->grant_type = 'password';
        $this->timeout = $timeout;
    }

    protected function checkUrl()
    {
        if($this->url === null){
            throw new ConfigException("No url for megaplan");
        }
    }

    /**
     * Проверка установлен ли username
     *
     * @return void
     */
    public function checkUsername()
    {
        if($this->username === null){
            throw new ConfigException("No username for megaplan");
        }
    }

    /**
     * Проверяем установлен ли пароль
     *
     * @return void
     */
    public function checkPassword()
    {
        if($this->password === null){
            throw new ConfigException("No password for megaplan");
        }
    }


    /**
     * Проверяем установлен ли grant_type
     *
     * @return void
     */
    public function checkGrantType()
    {
        if($this->grant_type === null){
            throw new ConfigException("No grant type for megaplan");
        }
    }


    /**
     * Проверка установлен ли timeout
     *
     * @return void
     */
    public function checkTimeout()
    {
        if($this->timeout === null){
            throw new ConfigException("No timeout for megaplan");
        }
    }


    /**
     * Загрузка файла
     *
     * @param string $path - путь к файлу
     * @return void
     */
    public function upload($path)
    {
//        $this->auth();
//        $action = '/api/file';
//        // $path = storage_path($path);
//        $type = pathinfo($path, PATHINFO_EXTENSION);
//        $name = pathinfo($path, PATHINFO_BASENAME);
//        $data['files[]'] = new \CURLFile($path,'image/'.$type,$name);
//        $url = $this->getUrl().$action;
//
//        $headers = $this->getHeader();
//        $headers[] = 'AUTHORIZATION: Bearer '.$this->access_token;
//
//        $ch = curl_init( $url );
//        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
//        curl_setopt( $ch, CURLOPT_USERAGENT, __CLASS__ );
//        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
//        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt( $ch, CURLOPT_POST, true );
//        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
//
//        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
//        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
//        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
//		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->getTimeout() );
//        curl_setopt( $ch, CURLOPT_TIMEOUT, $this->getTimeout() );
//
//        $res = curl_exec( $ch );
//
//        if(isset($res->error)){
//            throw new SenddataException($res->error_description);
//        }
//
//        $res = json_decode($res);
//
//        if($res->meta->status == '404'){
//            $error = '';
//            foreach ($res->meta->errors as $e) {
//                $error = ' | '.$e->message;
//            }
//            throw new SenddataException($error);
//        }
//
//        if($res->meta->status == '403'){
//            $error = '';
//            foreach ($res->meta->errors as $e) {
//                $error = ' | '.$e->message;
//            }
//            throw new AuthException($error);
//        }
//
//        if($res->meta->status == '200'){
//            return $res;
//        }
//
//        //неизвестная ошибка
//        $error = '';
//        foreach ($res->meta->errors as $e) {
//            $error = ' | '.$e->message;
//        }
//        throw new MegaplanException($error);
    }

    /**
     * Авторизация и получение токена в мегаплане
     *
     * @return void
     */
    protected function auth()
    {
        $res = '';

        $this->checkUrl();
        $this->checkUsername();
        $this->checkPassword();

        $headers = $this->header;

        $url = $this->url.'/'.'api/v3/auth/access_token';

        $dateStart = time();

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, __CLASS__ );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, [
            'username' => $this->username,
            'password' => $this->password,
            'grant_type' => $this->grant_type,
        ] );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->timeout );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $this->timeout );
        $res = curl_exec( $ch );

        $res = json_decode($res);

        if(isset($res->error)){
            throw new SenddataException($res->error_description);
        }
        $res->dateStart = $dateStart;
        $data = json_encode($res);

        $this->access_token = $res->access_token;
        $this->token_type = $res->token_type;
        $this->refresh_token = $res->refresh_token;
        $this->expires_in = $res->expires_in;
        $this->scope = $res->scope;

        return true;
    }

    protected function send(array $data, array $pagination, $action , $method)
    {
        $this->auth();

        if(count($pagination)!=0){
            $pagination = json_encode($pagination);
            if($method = 'GET'){
                $url = $this->url.$action.'?'.$pagination;
            } else {
                $url = $this->url.$action;
            }
        } else {
            $url = $this->url.$action;
        }


        $headers = $this->header;
        $headers[] = 'AUTHORIZATION: Bearer '.$this->access_token;

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, __CLASS__ );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        if($method === 'POST'){
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->timeout );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $this->timeout );

        $res = curl_exec( $ch );

        if(isset($res->error)){
            throw new SenddataException($res->error_description);
        }

        $res = json_decode($res);

        if($res->meta->status == '404'){
            $error = '';
            foreach ($res->meta->errors as $e) {
                $error = ' | '.$e->message;
            }
            throw new SenddataException($error);
        }

        if($res->meta->status == '403'){
            $error = '';
            foreach ($res->meta->errors as $e) {
                $error = ' | '.$e->message;
            }
            throw new AuthException($error);
        }

        if($res->meta->status == '200'){
            return $res;
        }

        //неизвестная ошибка
        $error = '';
        foreach ($res->meta->errors as $e) {
            $error = ' | '.$e->message;
        }
        throw new MegaplanException($error);
    }
}