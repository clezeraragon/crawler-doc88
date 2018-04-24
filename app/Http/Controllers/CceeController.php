<?php

namespace Crawler\Http\Controllers;

use Crawler\Regex\RegexCceePldSemanal;
use Crawler\StorageDirectory\StorageDirectory;
use Goutte\Client;
use Crawler\Model\ArangoDb;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
use Carbon\Carbon;
class CceeController extends Controller
{
    private $storageDirectory;
    private $client;
    private $regexCceePldSemanal;
    private $arangoDb;

    public function __construct(StorageDirectory $storageDirectory, Client $client, RegexCceePldSemanal $regexCceePldSemanal, ArangoDb $arangoDb)
    {
        $this->storageDirectory = $storageDirectory;
        $this->client = $client;
        $this->regexCceePldSemanal = $regexCceePldSemanal;
        $this->arangoDb = $arangoDb;
    }

    public function historicoPrecoSemanal()
    {
        $url_base = "https://www.ccee.org.br/preco_adm/precos/historico/semanal/";

        $crawler = $this->client->request('GET', $url_base,array('allow_redirects' => true));
        $this->client->getCookieJar();


        $results  = explode('<table width="100%" class="displayTag-table_soma">',$this->regexCceePldSemanal->clearHtml($crawler->html()));
        $results = array_slice($results, 1);
        $sudeste_centro_oeste = [];
        $sul = [];
        $nordeste =[];
        $norte = [];
        $date = date('Y-m-d');

        foreach ($results as $result)
        {
            /** Sudeste/centro-Oeste */
            $sudeste_centro_oeste[][$date] = [
                'semana' => $this->regexCceePldSemanal->getSemana($result),
                'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                'pesada' => $this->regexCceePldSemanal->getSudesteCentroOestePesada($result),
                'media' => $this->regexCceePldSemanal->getSudesteCentroOesteMedia($result),
                'leve' => $this->regexCceePldSemanal->getSudesteCentroOesteLeve($result),
            ];
            /** ----------- */

            /** Sul */
            $sul[][$date] = [
                'semana' => $this->regexCceePldSemanal->getSemana($result),
                'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                'pesada' => $this->regexCceePldSemanal->getSulPesada($result),
                'media' => $this->regexCceePldSemanal->getSulMedia($result),
                'leve' => $this->regexCceePldSemanal->getSulLeve($result),
            ];
            /** ----------- */

            /** Nordeste */
            $nordeste[][$date] = [
                'semana' => $this->regexCceePldSemanal->getSemana($result),
                'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                'pesada' => $this->regexCceePldSemanal->getNordestePesada($result),
                'media' => $this->regexCceePldSemanal->getNordesteMedia($result),
                'leve' => $this->regexCceePldSemanal->getNordesteLeve($result),
            ];
            /** ---------- */

            /** Norte */
            $norte[][$date] = [
                'semana' => $this->regexCceePldSemanal->getSemana($result),
                'periodo_de' => $this->regexCceePldSemanal->getPeriodoDe($result),
                'periodo_ate' => $this->regexCceePldSemanal->getPeriodoAte($result),
                'pesada' => $this->regexCceePldSemanal->getNortePesada($result),
                'media' => $this->regexCceePldSemanal->getNorteMedia($result),
                'leve' => $this->regexCceePldSemanal->getNorteLeve($result),
            ];
            /** ---------- */

        }
        // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

        try {
            if ($this->arangoDb->collectionHandler()->has('Ccee')) {

                $this->arangoDb->documment()->set('sudeste_centro_oeste', $sudeste_centro_oeste);
                $this->arangoDb->documment()->set('sul', $sul);
                $this->arangoDb->documment()->set('nordeste', $nordeste);
                $this->arangoDb->documment()->set('norte', $norte);
                $id = $this->arangoDb->documentHandler()->save('Ccee', $this->arangoDb->documment());

            } else {

                // create a new collection
                $this->arangoDb->collection()->setName('Ccee');
                $this->arangoDb->documment()->set('sudeste_centro_oeste', $sudeste_centro_oeste);
                $this->arangoDb->documment()->set('sul', $sul);
                $this->arangoDb->documment()->set('nordeste', $nordeste);
                $this->arangoDb->documment()->set('norte', $norte);
                $id = $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());
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
            'responsabilidade' => 'Realizar a capitura semanal das informações na tabela',
            'status' => 'Crawler Ccee semanal realizado com sucesso!'
        ]);
    }
}
