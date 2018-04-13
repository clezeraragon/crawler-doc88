<?php

namespace Crawler\Http\Controllers;

use Crawler\Regex\RegexSdroDiario;
use Crawler\Regex\RegexSdroSemanal;
use Crawler\StorageDirectory\StorageDirectory;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
class OnsController extends Controller
{
    private $regexSdroSemanal;
    private $storageDirectory;
    private $regexSdroDiario;

    public function __construct(RegexSdroSemanal $regexSdroSemanal,
                                StorageDirectory $storageDirectory,
                                RegexSdroDiario $regexSdroDiario)
    {
      $this->regexSdroSemanal = $regexSdroSemanal;
      $this->storageDirectory = $storageDirectory;
      $this->regexSdroDiario = $regexSdroDiario;
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

        $results_download =  Curl::to($url_base.$url_download_xls)
            ->withContentType('application/xlsx')
            ->download('');
dump($results_download);die;
        $this->storageDirectory->saveDirectory('ons/diaria/',$url_download_xls_name,$results_download);


    }
}
