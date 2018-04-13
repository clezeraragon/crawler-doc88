<?php

namespace Crawler\Http\Controllers;

use Crawler\Regex\RegexSdroSemanal;
use Crawler\StorageDirectory\StorageDirectory;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
class OnsController extends Controller
{
    private $regexSdroSemanal;
    private $storageDirectory;

    public function __construct(RegexSdroSemanal $regexSdroSemanal,StorageDirectory $storageDirectory)
    {
      $this->regexSdroSemanal = $regexSdroSemanal;
      $this->storageDirectory = $storageDirectory;
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

        $results_download_nota_tecnica =  Curl::to($url_base.$data_de_ate.$url_download_xls)
            ->withContentType('application/xlsx')
            ->download('');
        $this->storageDirectory->saveDirectory('ons/semanal',$url_download_xls_name,$results_download_nota_tecnica);

       echo 'tudo ocorreu como esperado!';

    }
    public function mltEnas()
    {
       $url_base = "https://agentes.ons.org.br/download/operacao/hidrologia/arquivoMLTENAS_201709.pdf";

        $results_download =  Curl::to($url_base)
            ->withContentType('application/xlsx')
            ->download('');
        dump($results_download);
    }
}
http://sdro.ons.org.br/SDRO/semanal/2018_03_31_2018_04_06/Html/SEMANAL_31-03-2018.xlsx   2018_03_31_2018_04_06/index.htm