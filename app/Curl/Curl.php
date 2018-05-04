<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/04/18
 * Time: 11:03
 */

namespace Crawler\Curl;


class Curl
{

    protected $curl;

    protected $options;

//    const   CURLOPT_RETURNTRANSFER = true;
//    const   CURLOPT_HEADER = false;
//    const   CURLOPT_FOLLOWLOCATION = true;
//    const   CURLOPT_ENCODING = "";
//    const   CURLOPT_USERAGENT = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)";
//    const   CURLOPT_AUTOREFERER = true;
//    const   CURLOPT_CONNECTTIMEOUT = 120;
//    const   CURLOPT_TIMEOUT =  120;
//    const   CURLOPT_MAXREDIRS = 5;
//    const   CURLOPT_SSL_VERIFYHOST = 0;
//    const   CURLOPT_SSL_VERIFYPEER = false;
//    const   CURLOPT_VERBOSE = false;
//    const   CURLOPT_HTTPHEADER = '';
//    const   CURLOPT_COOKIEFILE = "cookiefile";
//    const   CURLOPT_COOKIEJAR = "cookiefile";


    public function __construct()
    {
        //retirado de um exemplo no php.net
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // browsers keep this blank.


        $this->options = array(
            CURLOPT_RETURNTRANSFER => true,          // retorna a página como string
            CURLOPT_HEADER         => false,         // não retorna headers
            CURLOPT_FOLLOWLOCATION => true,          // seguir Location: (redirects)
            CURLOPT_ENCODING       => "",            // handle all encodings
            CURLOPT_USERAGENT      => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36",  // UserAgent do http
            CURLOPT_AUTOREFERER    => true,          // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,           // tempo de espera para conectar (2 minutos)
            CURLOPT_TIMEOUT        => 120,           // tempo de espera para executar (2 minutos)
            CURLOPT_MAXREDIRS      => 5,             // número de redirects permitido
            CURLOPT_SSL_VERIFYHOST => 0,             // não verificar certificado SSL
            CURLOPT_SSL_VERIFYPEER => false,         // não verificar peer certificado
            CURLOPT_VERBOSE        => false,         // informações/logs
            CURLOPT_HTTPHEADER     => $header,       // headers do browser
            CURLOPT_COOKIEFILE     => "cookiefile",  // armazena os cookies
            CURLOPT_COOKIEJAR      => "cookiefile",   // armazena os cookies
            CURLINFO_HEADER_OUT    => false,
        );

        $this->curl = curl_init();
        curl_setopt_array($this->curl ,$this->options);
    }

    public function exeCurl($addOptions=null)
    {
        //echo"<pre>";
        //var_dump($this->options);

        if($addOptions)
            $this->configCurl($addOptions);

        //echo"<pre>";
        //var_dump($this->options);

        return curl_exec($this->curl);
    }

    public function configCurl($addOptions)
    {
        $this->options = array_replace_recursive($this->options, $addOptions);
        curl_setopt_array($this->curl ,$this->options);
    }

    public function closeCurl()
    {
        curl_close($this->curl);
    }

    public function statuspageCurl()
    {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }

    public function returnHeaderOut()
    {
        return curl_getinfo($this->curl, CURLINFO_HEADER_OUT);
    }
}