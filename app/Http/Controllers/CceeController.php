<?php

namespace Crawler\Http\Controllers;

use Crawler\Excel\ImportExcel;
use Crawler\Regex\RegexCceeInfoMercadoGeral;
use Crawler\StorageDirectory\StorageDirectory;
use Crawler\Util\Util;
use Goutte\Client;
use Carbon\Carbon;
use Crawler\Model\ArangoDb;
use Ixudra\Curl\Facades\Curl;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
use Crawler\Regex\RegexCceePldSemanal;
use Crawler\Regex\RegexCceePldMensal;


class CceeController extends Controller
{
    private $storageDirectory;
    private $client;
    private $regexCceePldSemanal;
    private $arangoDb;
    private $regexCceeInfoMercadoGeral;
    private $regexCceePldMensal;
    private $importExcel;

    public function __construct(StorageDirectory $storageDirectory,
                                Client $client,
                                RegexCceePldSemanal $regexCceePldSemanal,
                                RegexCceePldMensal $regexCceePldMensal,
                                RegexCceeInfoMercadoGeral $regexCceeInfoMercadoGeral,
                                ImportExcel $importExcel,
                                ArangoDb $arangoDb)
    {
        $this->storageDirectory = $storageDirectory;
        $this->client = $client;
        $this->regexCceePldSemanal = $regexCceePldSemanal;
        $this->regexCceePldMensal = $regexCceePldMensal;
        $this->arangoDb = $arangoDb;
        $this->regexCceeInfoMercadoGeral = $regexCceeInfoMercadoGeral;
        $this->importExcel = $importExcel;
    }
    public function historicoPrecoMensal()
    {
        $url_base = "https://www.ccee.org.br/preco/precoMedio.do";

        $crawler = $this->client->request('GET', $url_base, array('allow_redirects' => true));
        $this->client->getCookieJar();


        $results  = explode('<table class="displaytag-Table_soma">',$this->regexCceePldMensal->clearHtml($crawler->html()));


        foreach ($results as $result) {
            $mes_ano = $this->regexCceePldMensal->capturaMes($result);
        }

         $ano_mes = Util::getMesAno($mes_ano);

        foreach ($results as $result)
        {
            $atual['Mensal'][$ano_mes]= [
                'Sudeste_Centro-oeste' => $this->regexCceePldMensal->capturaSeCo($result),
                'Sul' => $this->regexCceePldMensal->capturaS($result),
                'Nordeste' => $this->regexCceePldMensal->capturaNe($result),
                'Norte' => $this->regexCceePldMensal->capturaN($result),
            ];

        }

        try {

            if ($this->arangoDb->collectionHandler()->has('ccee')) {

                $this->arangoDb->documment()->set('Pld', $atual);
                $this->arangoDb->documentHandler()->save('ccee', $this->arangoDb->documment());

            } else {

                // create a new collection
                $this->arangoDb->collection()->setName('ccee');

                $this->arangoDb->documment()->set('Pld', $atual);
                $this->arangoDb->collectionHandler()->create('ccee', $this->arangoDb->documment());
            }
        } catch (ArangoConnectException $e) {
            print 'Connection error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoClientException $e) {
            print 'Client error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoServerException $e) {
            print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
        }

        return response()->json([
            'site' => 'https://www.ccee.org.br//preco/precoMedio.do/',
            'responsabilidade' => 'Realizar a capitura mensal das informações na tabela Html',
            'status' => 'Crawler Ccee mensal realizado com sucesso!'
        ]);
    }

    public function historicoPrecoSemanal()
    {
        $url_base = "https://www.ccee.org.br/preco_adm/precos/historico/semanal/";

        $crawler = $this->client->request('GET', $url_base,array('allow_redirects' => true));
        $result_status = $this->client->getResponse();

        $this->client->getCookieJar();

        if($result_status->getStatus() == 200) {

            $results = explode('<table width="100%" class="displayTag-table_soma">', $this->regexCceePldSemanal->clearHtml($crawler->html()));
            $results = array_slice($results, 1);
            $sudeste_centro_oeste = [];
            $sul = [];
            $nordeste = [];
            $norte = [];
            $date = Util::getDateIso();

            foreach ($results as $result) {
                /** Sudeste/centro-Oeste */
                $sudeste_centro_oeste[] = [
                    'semana' => $this->regexCceePldSemanal->getSemana($result),
                    'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                    'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                    'pesada' => $this->regexCceePldSemanal->getSudesteCentroOestePesada($result),
                    'media' => $this->regexCceePldSemanal->getSudesteCentroOesteMedia($result),
                    'leve' => $this->regexCceePldSemanal->getSudesteCentroOesteLeve($result),
                ];
                /** ----------- */

                /** Sul */
                $sul[] = [
                    'semana' => $this->regexCceePldSemanal->getSemana($result),
                    'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                    'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                    'pesada' => $this->regexCceePldSemanal->getSulPesada($result),
                    'media' => $this->regexCceePldSemanal->getSulMedia($result),
                    'leve' => $this->regexCceePldSemanal->getSulLeve($result),
                ];
                /** ----------- */

                /** Nordeste */
                $nordeste[] = [
                    'semana' => $this->regexCceePldSemanal->getSemana($result),
                    'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                    'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                    'pesada' => $this->regexCceePldSemanal->getNordestePesada($result),
                    'media' => $this->regexCceePldSemanal->getNordesteMedia($result),
                    'leve' => $this->regexCceePldSemanal->getNordesteLeve($result),
                ];
                /** ---------- */

                /** Norte */
                $norte[] = [
                    'semana' => $this->regexCceePldSemanal->getSemana($result),
                    'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                    'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                    'pesada' => $this->regexCceePldSemanal->getNortePesada($result),
                    'media' => $this->regexCceePldSemanal->getNorteMedia($result),
                    'leve' => $this->regexCceePldSemanal->getNorteLeve($result),
                ];
                /** ---------- */

            }

            $resultados = [
                'sudeste_centro_oeste' => $sudeste_centro_oeste,
                'sul' => $sul,
                'nordeste' => $nordeste,
                'norte' => $norte

            ];

            // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

            try {

                if ($this->arangoDb->collectionHandler()->has('ccee')) {

                    $this->arangoDb->documment()->set($date, $resultados);
                    $this->arangoDb->documentHandler()->save('ccee', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('ccee');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());
                    // create a new documment
                    $this->arangoDb->documment()->set($date, $resultados);
                    $this->arangoDb->documentHandler()->save('ccee', $this->arangoDb->documment());

                }
            } catch (ArangoConnectException $e) {
                print 'Connection error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoClientException $e) {
                print 'Client error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoServerException $e) {
                print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
            }

            return response()->json([
                'site' => 'https://www.ccee.org.br/preco_adm/precos/historico/semanal/',
                'responsabilidade' => 'Realizar a capitura semanal das informações na tabela Html',
                'status' => 'Crawler Ccee semanal realizado com sucesso!'
            ]);
        }else{

            return response()->json([
                'site' => 'https://www.ccee.org.br/preco_adm/precos/historico/semanal/',
                'responsabilidade' => 'Realizar a capitura semanal das informações na tabela Html',
                'status' => 'O crawler não encontrou o arquivo especificado!'
            ]);
        }
    }
    public function getInfoMercadoGeralAndIndividual()
    {
        $carbon = Carbon::now();
        $date = $carbon->format('Y-m-d');

        $url_base = 'https://www.ccee.org.br/';
        $url_base_1 = 'https://www.ccee.org.br/portal/js/informacoes_mercado.js?_=1524754496465';
        $url_base_2 = 'https://www.ccee.org.br/portal/faces/oracle/webcenter/portalapp/pages/publico/oquefazemos/infos/abas_infomercado.jspx';

        $this->client->request('GET', $url_base_1,array('allow_redirects' => true));
        $crawler = $this->client->request('POST', $url_base_2,array('allow_redirects' => true,'aba' => 'aba_info_mercado_mensal'));


        $cookieJar = $this->client->getCookieJar();
        $get_response_site = $this->client->getResponse();
        $get_client = $this->client->getClient();

        $downloads = [];

        if($get_response_site->getStatus() == 200) {

            $downloads = [
                'geral' => $url_dowload_geral = $this->regexCceeInfoMercadoGeral->capturaUrlDownloadGeral($crawler->html()),
                'individual' => $url_dowload_individual = $this->regexCceeInfoMercadoGeral->capturaUrlDownloadIndividual($crawler->html()),
            ];

            foreach ($downloads as  $key => $download) {

                $mont_url_download = $url_base . $download;

                $jar = \GuzzleHttp\Cookie\CookieJar::fromArray($cookieJar->all(), $url_base);
                $response = $get_client->get($mont_url_download, ['cookies' => $jar, 'allow_redirects' => true]);

                $result_download = Curl::to($mont_url_download)
                    ->setCookieJar('down')
                    ->allowRedirect(true)
                    ->withContentType('application/xlsx')
                    ->download('');

                if($key == 'geral') {
                    $resultado['geral'][$date]['file'] = $this->storageDirectory->saveDirectory('ccee/mensal/'.$key.'/' . $date . '/', 'InfoMercado_Dados_Gerais.xlsx', $result_download);
                    // Importação dos dados da planilha
                    $sheet = 5; // 003 Consumo
                    $startRow = 15;
                    $takeRows = 86;
                    $resultado['geral'][$date]['data'] = $this->importExcel->consumoAneel(
                        storage_path('app') . '/' . $resultado['geral'][$date]['file'][0],
                        $sheet,
                        $startRow,
                        $takeRows,
                        $carbon
                    );
                }else{
                    $resultado['individual'][$date] = $this->storageDirectory->saveDirectory('ccee/mensal/'.$key.'/' . $date . '/', 'InfoMercado_Dados_Individuais.xlsx', $result_download);
                }
            }

            try {
                if ($this->arangoDb->collectionHandler()->has('ccee')) {

                    $this->arangoDb->documment()->set('geral', $resultado['geral']);
                    $this->arangoDb->documment()->set('individual', $resultado['individual']);
                    $this->arangoDb->documentHandler()->save('ccee', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('ccee');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());

                    $this->arangoDb->documment()->set('geral', $resultado['geral']);
                    $this->arangoDb->documment()->set('individual', $resultado['individual']);
                    $this->arangoDb->documentHandler()->save('ccee', $this->arangoDb->documment());
                }
            } catch (ArangoConnectException $e) {
                print 'Connection error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoClientException $e) {
                print 'Client error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoServerException $e) {
                print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
            }

            return response()->json([
                'site' => 'https://www.ccee.org.br/portal/faces/oracle/webcenter/portalapp/pages/publico/oquefazemos/infos/abas_infomercado.jspx',
                'responsabilidade' => 'Realizar o download do arquivo info-mercado',
                'status' => 'Crawler Ccee Info-Mercado-Geral e Individual mensal realizado com sucesso!'
            ]);

        }else{
            return response()->json([
                'site' => 'https://www.ccee.org.br/portal/faces/oracle/webcenter/portalapp/pages/publico/oquefazemos/infos/abas_infomercado.jspx',
                'responsabilidade' => 'Realizar o download do arquivo info-mercado',
                'status' => 'O crawler não encontrou o arquivo especificado!'
            ]);
        }
    }


}
