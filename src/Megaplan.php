<?php

namespace Zloykolobok\Megaplan3;

use Zloykolobok\Megaplan3\Exception\ConfigException;
use Zloykolobok\Megaplan3\Exception\SenddataException;
use Zloykolobok\Megaplan3\Exception\AuthException;
use Zloykolobok\Megaplan3\Exception\MegaplanException;
use Illuminate\Support\Facades\Storage;

class Megaplan
{

    protected $url;
    protected $username;
    protected $password;
    protected $header;
    protected $grant_type;
    protected $timeout;
    protected $session;

    protected $access_token;
    protected $expires_in;
    protected $token_type;
    protected $scope;
    protected $refresh_token;

    public function __construct()
    {
        if(config('megaplan3.url') == null or config('megaplan3.url') == '') {
            throw new ConfigException("No url for megaplan");
        }

        if(config('megaplan3.username') == null or config('megaplan3.username') == '') {
            throw new ConfigException("No username for megaplan");
        }

        if(config('megaplan3.password') == null or config('megaplan3.password') == '') {
            throw new ConfigException("No password for megaplan");
        }

        if(config('megaplan3.header') == null or config('megaplan3.header') == '') {
            throw new ConfigException("No header for megaplan");
        }

        if(config('megaplan3.grant_type') == null or config('megaplan3.grant_type') == '') {
            throw new ConfigException("No grant type for megaplan");
        }

        if(config('megaplan3.timeout') == null or config('megaplan3.timeout') == '') {
            throw new ConfigException("No timeout for megaplan");
        }

        if(config('megaplan3.session') == null or config('megaplan3.session') == '') {
            throw new ConfigException("No session for megaplan");
        }

        $this->url = config('megaplan3.url');
        $this->header = config('megaplan3.header');
        $this->username = config('megaplan3.username');
        $this->password = config('megaplan3.password');
        $this->grant_type = config('megaplan3.grant_type');
        $this->timeout = config('megaplan3.timeout');
        $this->session = config('megaplan3.session');

        $this->auth();
    }

    /**
     * Авторизация и получение токена в мегаплане
     *
     * @return void
     */
    protected function auth()
    {
        $res = '';
        if($this->session){
            if(Storage::exists('session.json')){
                $res = json_decode(Storage::get('session.json'));
                $dateNow = time();
                if($dateNow < $res->dateStart + $res->expires_in){
                    $this->access_token = $res->access_token;
                    $this->token_type = $res->token_type;
                    $this->refresh_token = $res->refresh_token;
                    $this->expires_in = $res->expires_in;
                    $this->scope = $res->scope;

                    return true;
                }
            }
        }

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

        if($this->session){
            Storage::put('session.json',$data);
        }

        $this->access_token = $res->access_token;
        $this->token_type = $res->token_type;
        $this->refresh_token = $res->refresh_token;
        $this->expires_in = $res->expires_in;
        $this->scope = $res->scope;

        return true;
    }

    protected function send(array $data, $action , $method)
    {
        $jsonData = $data;
        $jsonData = json_encode($jsonData);

        $url = $this->url.$action;

        $headers = $this->header;
        $headers[] = 'AUTHORIZATION: Bearer '.$this->access_token;

        $data = json_encode($data);

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, __CLASS__ );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );

        if($method === 'POST'){
            // curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type' => 'application/json'] );
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