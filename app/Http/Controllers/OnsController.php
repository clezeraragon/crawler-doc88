<?php

namespace Crawler\Http\Controllers;

use Carbon\Carbon;
use Crawler\Regex\RegexSdroDiario;
use Crawler\Regex\RegexSdroSemanal;
use Crawler\StorageDirectory\StorageDirectory;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Goutte\Client;
use Crawler\Regex\RegexMltEnas;
use Crawler\Model\ArangoDb;


class OnsController extends Controller
{
    private $regexSdroSemanal;
    private $storageDirectory;
    private $regexSdroDiario;
    private $client;
    private $regexMltEnas;
    private $arangoDb;

    public function __construct(RegexSdroSemanal $regexSdroSemanal,
                                StorageDirectory $storageDirectory,
                                Client $client,
                                ArangoDb $arangoDb,
                                RegexMltEnas $regexMltEnas,
                                RegexSdroDiario $regexSdroDiario)
    {
        $this->regexSdroSemanal = $regexSdroSemanal;
        $this->storageDirectory = $storageDirectory;
        $this->regexSdroDiario = $regexSdroDiario;
        $this->client = $client;
        $this->regexMltEnas = $regexMltEnas;
        $this->arangoDb = $arangoDb;
    }


    public function sdroSemanal()
    {
        $url_base = "http://sdro.ons.org.br/SDRO/semanal/";

        $response = Curl::to($url_base)
            ->returnResponseObject()
            ->get();

        if($response->status == 200) {

            $url = $this->regexSdroSemanal->capturaUrlAtual($response->content);
            $response_2 = Curl::to($url_base . $url)
                ->get();

            $data_de_ate = $this->regexSdroSemanal->capturaUrlData($url);
            $url_download_xls = $this->regexSdroSemanal->capturaUrlDownloadExcel($response_2);
            $url_download_xls_name = $this->regexSdroSemanal->capturaUrlDownloadName($url_download_xls);


            $results_download = Curl::to($url_base . $data_de_ate . $url_download_xls)
                ->withContentType('application/xlsx')
                ->download('');
            $url_download['url_download_semanal'] = $this->storageDirectory->saveDirectory('ons/semanal/', $url_download_xls_name, $results_download);

            // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

            try {
                if ($this->arangoDb->collectionHandler()->has('ons')) {

                    $this->arangoDb->documment()->set('ons_semanal', $url_download);
                    $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('ons');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());
                    $this->arangoDb->documment()->set('ons_semanal', $url_download);
                    $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());
                }
            } catch (ArangoConnectException $e) {
                print 'Connection error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoClientException $e) {
                print 'Client error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoServerException $e) {
                print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
            }

            return response()->json([
                'site' => 'http://sdro.ons.org.br/SDRO/semanal/',
                'responsabilidade' => 'Realizar download do arquivo semanal Ons',
                'status' => 'Crawler Sdro Semanal realizado com sucesso!'
            ]);
        }
    }


    public function sdroDiario()
    {
        $url_base = "http://sdro.ons.org.br/SDRO/DIARIO/";

        $response = Curl::to($url_base)
            ->returnResponseObject()
            ->get();

        if($response->status == 200) {

            $url = $this->regexSdroDiario->capturaUrlAtual($response->content);
            $response_2 = Curl::to($url_base . $url)
                ->get();

            $url_download_xls = $this->regexSdroDiario->capturaUrlDownloadExcel($response_2);
            $url_download_xls_name = $this->regexSdroDiario->capturaUrlDownloadName($url_download_xls);
            $capitura_name = $this->regexSdroDiario->capturaUrlData($url_download_xls_name);
            $mont_url_dowload = $url_base . $capitura_name . '/' . $url_download_xls;

            $results_download = Curl::to($mont_url_dowload)
                ->withContentType('application/xlsx')
                ->download('');

            $url_download['url_download_sdro'] = $this->storageDirectory->saveDirectory('ons/diaria/', $url_download_xls_name, $results_download);
            // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

            try {
                if ($this->arangoDb->collectionHandler()->has('ons')) {

                    $this->arangoDb->documment()->set('ons_boletim_diario', $url_download);
                    $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('ons');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());
                    $this->arangoDb->documment()->set('ons_boletim_diario', $url_download);
                    $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());
                }
            } catch (ArangoConnectException $e) {
                print 'Connection error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoClientException $e) {
                print 'Client error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoServerException $e) {
                print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
            }
            return response()->json([
                'site' => 'http://sdro.ons.org.br/SDRO/DIARIO/',
                'responsabilidade' => 'Realizar download do arquivo diario Ons',
                'status' => 'Crawler Sdro Diario realizado com sucesso!'
            ]);
        }
    }

    public function operacaoEnasDiario()
    {

        $url_base = "https://agentes.ons.org.br/";

        $crawler = $this->client->request('GET', 'https://pops.ons.org.br/ons.pop.federation/?ReturnUrl=https%3a%2f%2fagentes.ons.org.br%2foperacao%2fenas_subsistemas.aspx',array('allow_redirects' => true));
        $get_response_site = $this->client->getResponse();

        if($get_response_site->getStatus() == 200) {

            $form = $crawler->selectButton('Entrar')->form();
            $this->client->submit($form, array('username' => 'victor.shinohara', 'password' => 'comerc@12345'));
            $this->client->getCookieJar();

            $response = $this->client->request('GET', 'https://agentes.ons.org.br/operacao/enas_subsistemas.aspx');

            $results = $this->regexMltEnas->capturaDowloadMltEnas($response->html());
            $captura_name = $this->regexMltEnas->capturaNameArquivo($results);

            $url_dowload = $url_base . $results;

            $results_download = Curl::to($url_dowload)
                ->withContentType('application/xlsx')
                ->download('');

            $url_download['url_download_mlt_semanal'] = $this->storageDirectory->saveDirectory('ons/mlt/semanal', $captura_name, $results_download);
            // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

            try {
                if ($this->arangoDb->collectionHandler()->has('ons')) {

                    $this->arangoDb->documment()->set('ons_enas_diario', $url_download);
                    $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('ons');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());
                    $this->arangoDb->documment()->set('ons_enas_diario', $url_download);
                    $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());
                }
            } catch (ArangoConnectException $e) {
                print 'Connection error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoClientException $e) {
                print 'Client error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoServerException $e) {
                print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
            }
            return response()->json([
                'site' => 'https://agentes.ons.org.br/',
                'responsabilidade' => 'Realizar download do arquivo enas diario',
                'status' => 'Crawler Enas Diario realizado com sucesso!'
            ]);
        }
    }

    public function getAcervoDigitalDiario()
    {
        $date = Carbon::now()->subDay(1);
        $date_format = $date->format('d-m-Y');
        $ext = '.pdf';

        $crawler = $this->client->request('GET', 'http://ons.org.br/_layouts/download.aspx?SourceUrl=http://ons.org.br/AcervoDigitalDocumentosEPublicacoes/IPDO-'.$date_format.$ext);
        $cookieJar = $this->client->getCookieJar();
        $this->client->getClient();
        \GuzzleHttp\Cookie\CookieJar::fromArray($cookieJar->all(), 'http://ons.org.br/');

        $results_download = Curl::to($crawler->getBaseHref())
            ->withContentType('application/pdf')
            ->download('');
        $url_download[$date_format]['url_download_ipdo_diario'] = $this->storageDirectory->saveDirectory('ons/ipdo/'.$date_format.'/', 'IPDO-'.$date_format.$ext, $results_download);


        // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

        try {
            if ($this->arangoDb->collectionHandler()->has('ons')) {

                $this->arangoDb->documment()->set('ons_ipdo', $url_download);
                $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());

            } else {

                // create a new collection
                $this->arangoDb->collection()->setName('ons');
                $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());
                $this->arangoDb->documment()->set('ons_ipdo', $url_download);
                $this->arangoDb->documentHandler()->save('ons', $this->arangoDb->documment());
            }
        } catch (ArangoConnectException $e) {
            print 'Connection error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoClientException $e) {
            print 'Client error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoServerException $e) {
            print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
        }

        return response()->json([
            'site' => 'http://ons.org.br/',
            'responsabilidade' => 'Realizar download do arquivo IPDO(informativo preliminar diário operacional).',
            'status' => 'Crawler IPDO realizado com sucesso!'
        ]);
    }

}

