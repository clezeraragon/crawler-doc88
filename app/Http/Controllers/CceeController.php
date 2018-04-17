<?php

namespace Crawler\Http\Controllers;

use Crawler\Regex\RegexCceePldSemanal;
use Illuminate\Http\Request;
use Crawler\StorageDirectory\StorageDirectory;
use Ixudra\Curl\Facades\Curl;
use Goutte\Client;
use Crawler\Regex\RegexMltEnas;

class CceeController extends Controller
{
    private $storageDirectory;
    private $client;
    private $regexCceePldSemanal;

    public function __construct(StorageDirectory $storageDirectory,Client $client, RegexCceePldSemanal $regexCceePldSemanal)
    {
        $this->storageDirectory = $storageDirectory;
        $this->client = $client;
        $this->regexCceePldSemanal = $regexCceePldSemanal;
    }

    public function historicoPrecoSemanal()
    {
        $url_base = "https://www.ccee.org.br/preco_adm/precos/historico/semanal/";

        $crawler = $this->client->request('GET', $url_base,array('allow_redirects' => true));
        $this->client->getCookieJar();


        $results  = explode('<tbody>',$this->regexCceePldSemanal->clearHtml($crawler->html()));
        foreach ($results as $result)
        {
            /** Sudeste/centro-Oeste */
            $linha_top_de = $this->regexCceePldSemanal->getPeriodoDe($result);
            $linha_top_ate = $this->regexCceePldSemanal->getPeriodoAte($result);
            $coluna_1_pesada = $this->regexCceePldSemanal->getSudesteCentroOestePesada($result);
            $coluna_2_media = $this->regexCceePldSemanal->getSudesteCentroOesteMedia($result);
            $coluna_3_leve = $this->regexCceePldSemanal->getSudesteCentroOesteLeve($result);
           /** ----------- */

            /** Sul */
            $coluna_4_pesada = $this->regexCceePldSemanal->getSulPesada($result);
            $coluna_5_media = $this->regexCceePldSemanal->getSulMedia($result);
            $coluna_6_leve = $this->regexCceePldSemanal->getSulLeve($result);
            /** ----------- */

            /** Nordeste */
            $coluna_7_pesada = $this->regexCceePldSemanal->getNordestePesada($result);
            $coluna_8_media = $this->regexCceePldSemanal->getNordesteMedia($result);
            $coluna_9_leve = $this->regexCceePldSemanal->getNordesteLeve($result);
            /** ---------- */

            /** Norte */
            $coluna_10_pesada = $this->regexCceePldSemanal->getNortePesada($result);
            $coluna_11_media = $this->regexCceePldSemanal->getNorteMedia($result);
            $coluna_12_leve = $this->regexCceePldSemanal->getNorteLeve($result);
            /** ---------- */

          var_dump($coluna_10_pesada);

        }
        var_dump($results);
    }
}
https://www.ccee.org.br/preco_adm/precos/historico/semanal/