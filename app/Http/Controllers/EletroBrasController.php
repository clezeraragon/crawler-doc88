<?php

namespace Crawler\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Crawler\StorageDirectory\StorageDirectory;
use Ixudra\Curl\Facades\Curl;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
use Crawler\Model\ArangoDb;

class EletroBrasController extends Controller
{
    private $storageDirectory;
    private $arangoDb;


    public function __construct(StorageDirectory $storageDirectory, ArangoDb $arangoDb)
    {
        $this->storageDirectory = $storageDirectory;
        $this->arangoDb = $arangoDb;
    }

    public function getCde()
    {
        $carbon = Carbon::now();
        $ano = $carbon->year;

        $url = 'http://eletrobras.com/pt/FundosSetoriaisCDE/CDE%20-%20Movimenta%C3%A7%C3%A3o%20Financeira%20-%20' . '2017' . '.xls';
        $response = Curl::to($url)
            ->returnResponseObject()
            ->withContentType('application/xls')
            ->download('');

        if ($response->status == 200) {

            $resultado = $this->storageDirectory->saveDirectory('eletrobras/'.$ano.'/', 'CDE-'.$ano.'-Movimentação_Finaceira.xlsx', $response);


            try {
                if ($this->arangoDb->collectionHandler()->has('eletrobras')) {

                    $this->arangoDb->documment()->set('cde', $resultado);
                    $this->arangoDb->documentHandler()->save('eletrobras', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('eletrobras');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());

                    $this->arangoDb->documment()->set('cde', $resultado);
                    $this->arangoDb->documentHandler()->save('eletrobras', $this->arangoDb->documment());
                }
            } catch (ArangoConnectException $e) {
                print 'Connection error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoClientException $e) {
                print 'Client error: ' . $e->getMessage() . PHP_EOL;
            } catch (ArangoServerException $e) {
                print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
            }
            return response()->json([
                'site' => 'http://eletrobras.com',
                'responsabilidade' => 'Realiza o download cde movimentação financeira!',
                'status' => 'Crawler realizado com sucesso!'
            ]);

        }
        return response()->json([
            'site' => 'http://eletrobras.com',
            'responsabilidade' => 'Realiza o download cde movimentação financeira!',
            'status' => 'O crawler não encontrou o arquivo especificado!'
        ]);
    }

}
