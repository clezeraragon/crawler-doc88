<?php

namespace Crawler\Http\Controllers;

use Illuminate\Http\Request;
use Crawler\StorageDirectory\StorageDirectory;
use Ixudra\Curl\Facades\Curl;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
use Crawler\Model\ArangoDb;
use Carbon\Carbon;

class EpeConsumoController extends Controller
{
    private $storageDirectory;
    private $arangoDb;

    public function __construct(StorageDirectory $storageDirectory,ArangoDb $arangoDb)

    {
        $this->storageDirectory = $storageDirectory;
        $this->arangoDb = $arangoDb;
    }//
    public function getConsumo()
    {
        $carbon = Carbon::now();
        $date = $carbon->format('Y-m-d');

        $url_base = "www.epe.gov.br/sites-pt/publicacoes-dados-abertos/publicacoes/PublicacoesArquivos/publicacao-190/MERCADO%20MENSAL%20PARA%20DOWNLOAD%20CONSUMO.XLS";

        $results_download_status =  Curl::to($url_base)
            ->returnResponseObject()
            ->get();


        if($results_download_status->status == 200) {

            $results_download =  Curl::to($url_base)
                ->withContentType('application/xlsx')
                ->download('');

            $resultado = $this->storageDirectory->saveDirectory('epe/' . $date . '/', 'MERCADO_MENSAL_PARA_DOWNLOAD_CONSUMO.xlsx', $results_download);

            try {
                if ($this->arangoDb->collectionHandler()->has('epe')) {

                    $this->arangoDb->documment()->set('consumo', $resultado);
                    $this->arangoDb->documentHandler()->save('epe', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('epe');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());

                    $this->arangoDb->documment()->set('consumo', $resultado);
                    $this->arangoDb->documentHandler()->save('epe', $this->arangoDb->documment());
                }
            } catch (ArangoConnectException $e) {
                print 'Connection error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoClientException $e) {
                print 'Client error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoServerException $e) {
                print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
            }
            return response()->json([
                'site' => 'www.epe.gov.br',
                'responsabilidade' => 'Realizar download do arquivo EPE consumo!.',
                'status' => 'Crawler EPE realizado com sucesso!'
            ]);

        }
        return response()->json([
            'site' => 'www.epe.gov.br',
            'responsabilidade' => 'Realizar download do arquivo EPE consumo!.',
            'status' => 'O crawler não encontrou o arquivo especificado!'
        ]);

    }
}
