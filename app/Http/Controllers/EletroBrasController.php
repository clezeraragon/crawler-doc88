<?php

namespace Crawler\Http\Controllers;

use Carbon\Carbon;
use Crawler\Regex\RegexEletrobras;
use Crawler\Util\Util;
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
    private $regexEletrobras;


    public function __construct(StorageDirectory $storageDirectory, ArangoDb $arangoDb, RegexEletrobras $regexEletrobras)
    {
        $this->storageDirectory = $storageDirectory;
        $this->arangoDb = $arangoDb;
        $this->regexEletrobras = $regexEletrobras;
    }

    public function getCde()
    {
        $carbon = Carbon::now();
        $ano = $carbon->year;
        $url_base = 'http://eletrobras.com';

        $url = $url_base.'/pt/FundosSetoriaisCDE/Forms/AllItems.aspx';
        $response = Curl::to($url)
            ->returnResponseObject()
            ->setCookieFile('down')
            ->get();

        $url_movimentacao = $this->regexEletrobras->capturaUrlMovimentacao($response->content);
        $mount_url_dowload = $url_base.$url_movimentacao;

        $url_download = Curl::to($mount_url_dowload)
            ->setCookieFile('down')
            ->allowRedirect(true)
            ->withContentType('application/xlsx')
            ->download('');


        if ($response->status == 200) {

            $resultado = $this->storageDirectory->saveDirectory('eletrobras/'.$ano.'/', 'CDE-'.$ano.'-Movimentação_Finaceira.xlsx', $url_download);


            try {
                if ($this->arangoDb->collectionHandler()->has('eletrobras')) {

                    $this->arangoDb->documment()->set('cde', [Util::getDateIso() => $resultado]);
                    $this->arangoDb->documentHandler()->save('eletrobras', $this->arangoDb->documment());

                } else {

                    // create a new collection
                    $this->arangoDb->collection()->setName('eletrobras');
                    $this->arangoDb->collectionHandler()->create($this->arangoDb->collection());

                    $this->arangoDb->documment()->set('cde', [Util::getDateIso() => $resultado]);
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
