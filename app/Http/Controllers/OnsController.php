<?php

namespace Crawler\Http\Controllers;

use Crawler\Regex\RegexSdroDiario;
use Crawler\Regex\RegexSdroSemanal;
use Crawler\StorageDirectory\StorageDirectory;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Goutte\Client;
use Crawler\Regex\RegexMltEnas;

class OnsController extends Controller
{
    private $regexSdroSemanal;
    private $storageDirectory;
    private $regexSdroDiario;
    private $client;
    private $regexMltEnas;

    public function __construct(RegexSdroSemanal $regexSdroSemanal,
                                StorageDirectory $storageDirectory,
                                Client $client,
                                RegexMltEnas $regexMltEnas,
                                RegexSdroDiario $regexSdroDiario)
    {
        $this->regexSdroSemanal = $regexSdroSemanal;
        $this->storageDirectory = $storageDirectory;
        $this->regexSdroDiario = $regexSdroDiario;
        $this->client = $client;
        $this->regexMltEnas = $regexMltEnas;
    }


    public function sdroSemanal()
    {
        $url_base = "http://sdro.ons.org.br/SDRO/semanal/";

        $response = Curl::to($url_base)
            ->get();

        $url = $this->regexSdroSemanal->capturaUrlAtual($response);
        $response_2 = Curl::to($url_base.$url)
            ->get();

        $data_de_ate = $this->regexSdroSemanal->capturaUrlData($url);
        $url_download_xls = $this->regexSdroSemanal->capturaUrlDownloadExcel($response_2);
        $url_download_xls_name = $this->regexSdroSemanal->capturaUrlDownloadName($url_download_xls);


        $results_download =  Curl::to($url_base.$data_de_ate.$url_download_xls)
            ->withContentType('application/xlsx')
            ->download('');
        $this->storageDirectory->saveDirectory('ons/semanal/',$url_download_xls_name,$results_download);

        echo 'tudo ocorreu como esperado!';

    }
    public function mltEnas()
    {
        $url_base = "https://agentes.ons.org.br/download/operacao/hidrologia/arquivoMLTENAS_201709.pdf";

        $results_download =  Curl::to($url_base)
            ->withContentType('application/pdf')
            ->download('');
        $this->storageDirectory->saveDirectory('ons/diaria/','arquivoMLTENAS_201709.pdf',$results_download);

    }

    public function sdroDiario()
    {
        $url_base = "http://sdro.ons.org.br/SDRO/DIARIO/";

        $response = Curl::to($url_base)
            ->get();
        $url = $this->regexSdroDiario->capturaUrlAtual($response);
        $response_2 = Curl::to($url_base.$url)
            ->get();

        $url_download_xls = $this->regexSdroDiario->capturaUrlDownloadExcel($response_2);
        $url_download_xls_name = $this->regexSdroDiario->capturaUrlDownloadName($url_download_xls);
        $capitura_name = $this->regexSdroDiario->capturaUrlData($url_download_xls_name);
        $mont_url_dowload = $url_base.$capitura_name.'/'.$url_download_xls;

        $results_download =  Curl::to($mont_url_dowload)
            ->withContentType('application/xlsx')
            ->download('');

        $this->storageDirectory->saveDirectory('ons/diaria/',$url_download_xls_name,$results_download);

    }

    public function operacaoEnasSemanal()
    {

        $url_base = "https://agentes.ons.org.br/";

        $crawler = $this->client->request('GET', 'https://pops.ons.org.br/ons.pop.federation/?ReturnUrl=https%3a%2f%2fagentes.ons.org.br%2foperacao%2fenas_subsistemas.aspx',array('allow_redirects' => true));
        $form = $crawler->selectButton('Entrar')->form();
        $this->client->submit($form, array('username' => 'victor.shinohara', 'password' => 'comerc@12345'));
        $this->client->getCookieJar();

        $response =  $this->client->request('GET','https://agentes.ons.org.br/operacao/enas_subsistemas.aspx');


        $results =$this->regexMltEnas->capturaDowloadMltEnas($response->html());
        $captura_name = $this->regexMltEnas->capturaNameArquivo($results);

        $url_dowload = $url_base.$results;

        $results_download =  Curl::to($url_dowload)
            ->withContentType('application/xlsx')
            ->download('');

        $this->storageDirectory->saveDirectory('ons/mlt/semanal',$captura_name,$results_download);

    }

}
