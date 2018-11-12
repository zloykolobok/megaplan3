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

    public function __construct()
    {
        // if(config('megaplan3.url') == null or config('megaplan3.url') == '') {
        //     throw new ConfigException("No url for megaplan");
        // }

        // if(config('megaplan3.username') == null or config('megaplan3.username') == '') {
        //     throw new ConfigException("No username for megaplan");
        // }

        // if(config('megaplan3.password') == null or config('megaplan3.password') == '') {
        //     throw new ConfigException("No password for megaplan");
        // }

        // if(config('megaplan3.header') == null or config('megaplan3.header') == '') {
        //     throw new ConfigException("No header for megaplan");
        // }

        // if(config('megaplan3.grant_type') == null or config('megaplan3.grant_type') == '') {
        //     throw new ConfigException("No grant type for megaplan");
        // }

        // if(config('megaplan3.timeout') == null or config('megaplan3.timeout') == '') {
        //     throw new ConfigException("No timeout for megaplan");
        // }

        // if(config('megaplan3.session') == null or config('megaplan3.session') == '') {
        //     throw new ConfigException("No session for megaplan");
        // }

        // $this->url = config('megaplan3.url');
        // $this->header = config('megaplan3.header');
        // $this->username = config('megaplan3.username');
        // $this->password = config('megaplan3.password');
        // $this->grant_type = config('megaplan3.grant_type');
        // $this->timeout = config('megaplan3.timeout');
        // $this->session = config('megaplan3.session');

        // $this->auth();
    }

    /**
     * Установка URL
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url){
        $this->url = preg_replace("#/$#", "", $url);
    }

    /**
     * Получаем URL
     *
     * @return void
     */
    protected function getUrl()
    {
        return $this->url;
    }

    /**
     * Проверка установки URL
     *
     * @return void
     */
    protected function checkUrl()
    {
        if($this->url === null){
            throw new ConfigException("No url for megaplan");
        }
    }

    /**
     * Установка HEADER
     *
     * @param array $header
     * @return void
     */
    public function setHeader($header = ['content-type: multipart/form-data'])
    {
        $this->header = $header;
    }

    /**
     * Получаем HEADER
     *
     * @return void
     */
    protected function getHeader()
    {
        return $this->header;
    }

    /**
     * Проверяем установлен ли HEADER
     *
     * @return void
     */
    protected function checkHeader()
    {
        if($this->header === null){
            throw new ConfigException("No header for megaplan");
        }
    }

    /**
     * Установка Username
     *
     * @param string $username
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Получение username
     *
     * @return void
     */
    public function getUsername()
    {
        return $this->username;
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
     * Установка пароля
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Получаем пароль
     *
     * @return void
     */
    public function getPassword()
    {
        return $this->password;
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
     * Установка grant_type
     *
     * @param string $grant_type
     * @return void
     */
    public function setGrantType($grant_type = 'password')
    {
        $this->grant_type = $grant_type;
    }

    /**
     * Получаем $grant_type
     *
     * @return void
     */
    public function getGrantType()
    {
        return $this->grant_type;
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
     * Устанавливаем timeout
     *
     * @param int $timeout
     * @return void
     */
    public function setTimeout($timeout = 60)
    {
        $this->timeout = $timeout;
    }

    /**
     * Получаем timeout
     *
     * @return void
     */
    public function getTimeout()
    {
        return $this->timeout;
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
        $this->auth();
        $action = '/api/file';
        // $path = storage_path($path);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $name = pathinfo($path, PATHINFO_BASENAME);
        $data['files[]'] = new \CURLFile($path,'image/'.$type,$name);
        $url = $this->getUrl().$action;

        $headers = $this->getHeader();
        $headers[] = 'AUTHORIZATION: Bearer '.$this->access_token;

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, __CLASS__ );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->getTimeout() );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $this->getTimeout() );

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
        $this->checkHeader();
        $this->checkGrantType();
        $this->checkTimeout();

        // if($this->session){
        //     if(Storage::exists('session.json')){
        //         $res = json_decode(Storage::get('session.json'));
        //         $dateNow = time();
        //         if($dateNow < $res->dateStart + $res->expires_in){
        //             $this->access_token = $res->access_token;
        //             $this->token_type = $res->token_type;
        //             $this->refresh_token = $res->refresh_token;
        //             $this->expires_in = $res->expires_in;
        //             $this->scope = $res->scope;

        //             return true;
        //         }
        //     }
        // }

        $headers = $this->getHeader();

        $url = $this->getUrl().'/'.'api/v3/auth/access_token';

        $dateStart = time();

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, __CLASS__ );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, [
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'grant_type' => $this->getGrantType(),
        ] );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->getTimeout() );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $this->getTimeout() );
        $res = curl_exec( $ch );

        $res = json_decode($res);
        if(isset($res->error)){
            throw new SenddataException($res->error_description);
        }
        $res->dateStart = $dateStart;
        $data = json_encode($res);

        // if($this->session){
        //     Storage::put('session.json',$data);
        // }

        $this->access_token = $res->access_token;
        $this->token_type = $res->token_type;
        $this->refresh_token = $res->refresh_token;
        $this->expires_in = $res->expires_in;
        $this->scope = $res->scope;

        return true;
    }

    protected function send(array $data, $action , $method)
    {
        $this->auth();
        $jsonData = $data;
        $jsonData = json_encode($jsonData);

        $url = $this->getUrl().$action;

        $headers = $this->getHeader();
        $headers[] = 'AUTHORIZATION: Bearer '.$this->access_token;

        $data = json_encode($data);

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
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $this->getTimeout() );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $this->getTimeout() );

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