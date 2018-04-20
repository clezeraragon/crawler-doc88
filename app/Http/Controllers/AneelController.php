<?php

namespace Crawler\Http\Controllers;


use Crawler\Model\AragonDb;
use Crawler\Regex\RegexAneel;
use Crawler\StorageDirectory\StorageDirectory;
use http\Env\Response;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
class AneelController extends Controller
{

    private $regexAneel;
    private $storageDirectory;
    private $aragonDb;

    public function __construct( RegexAneel $regexAneel,StorageDirectory $storageDirectory,AragonDb $aragonDb)
    {
        $this->regexAneel = $regexAneel;
        $this->storageDirectory = $storageDirectory;
        $this->aragonDb = $aragonDb;
    }

    public function proInfa()
    {


        $response = Curl::to('http://biblioteca.aneel.gov.br/asp/resultadoFrame.asp?modo_busca=legislacao&content=resultado&iBanner=0&iEscondeMenu=0&iSomenteLegislacao=0&iIdioma=0&BuscaSrv=1')
            ->withData(
                array(
                    'leg_campo1' => 'Proinfa' ,
                    'leg_ordenacao' => 'publicacaoDESC',
                    'leg_normas' => '2',
                    'submeteu' => 'legislacao'
                ) )
            ->post();

        preg_match_all('/td_grid_ficha_background...(.*)/', $this->regexAneel->convert_str($response), $matches);



        $material = $this->regexAneel->capturaMaterial($matches[0][0]);
        $norma = $this->regexAneel->capturaNorma($matches[0][0]);
        $data_de_assinatura = $this->regexAneel->capturaDataAssinatura($matches[0][0]);
        $data_de_publicacao = $this->regexAneel->capturaDataPublicacao($matches[0][0]);
        $ementa = $this->regexAneel->capturaEmenta($matches[0][0]);
        $orgao_de_origem = $this->regexAneel->capturaOrgaoDeOriem($matches[0][0]);
        $esfera = $this->regexAneel->capturaEsfera($matches[0][0]);
        $situacao = $this->regexAneel->capturaSituacao($matches[0][0]);
        $texto_integral = $this->regexAneel->capturaTextoIntegral($matches[0][0]); // download
        $voto = $this->regexAneel->capturaVoto($matches[0][0]); //download
        $nota_tecnica = $this->regexAneel->capturaNotaTecnica($matches[0][0]); //download


        $download_texto_integral = [];
        $download_voto = [];
        $download_nota_tecnica = [];

        foreach ($norma as $key => $item )
        {
            if(isset($texto_integral[$key]['texto_integral'])) {
                $results_download_integral = Curl::to($texto_integral[$key]['texto_integral'])
                    ->withContentType('application/pdf')
                    ->download('');
                $download_texto_integral = $this->storageDirectory->saveDirectory('aneel/texto_integral', $texto_integral[$key]['name_arquivo'], $results_download_integral);
            }else{
                $download_texto_integral = array_merge($download_texto_integral,['vazio']);
            }
            if(isset($voto[$key]['voto'])) {
                $results_download_voto = Curl::to($voto[$key]['voto'])
                    ->withContentType('application/pdf')
                    ->download('');
                $download_voto = $this->storageDirectory->saveDirectory('aneel/voto', $voto[$key]['name_arquivo'], $results_download_voto);
            }else{
                $download_voto = array_merge($download_voto,['vazio']);
            }
            if(isset($nota_tecnica[$key]['nota_tecnica'])) {
                $results_download_nota_tecnica = Curl::to($nota_tecnica[$key]['nota_tecnica'])
                    ->withContentType('application/pdf')
                    ->download('');
                $download_nota_tecnica = $this->storageDirectory->saveDirectory('aneel/nota_tecnica', $nota_tecnica[$key]['name_arquivo'], $results_download_nota_tecnica);

            }else{
                $download_nota_tecnica = array_merge($download_nota_tecnica,['vazio']);
            }

        }
        foreach ($material as $key => $item)
        {
            $results[$key] = [
                'url_arquivo_nota_tecnica' => $download_nota_tecnica[$key],
                'url_arquivo_voto' => $download_voto[$key],
                'url_arquivo_texto_integral' => $download_texto_integral[$key]
            ];
            $resultados[] = array_merge($material[$key],$norma[$key],$data_de_assinatura[$key],$data_de_publicacao[$key],$ementa[$key],$orgao_de_origem[$key],$esfera[$key],$situacao[$key],$results[$key]);
        }
        // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

        try {
            if ($this->aragonDb->collectionHandler()->has('aneel_proinfa')) {

                $this->aragonDb->documment()->set('proInfa', $resultados);
                $id = $this->aragonDb->documentHandler()->save('aneel_proinfa', $this->aragonDb->documment());

            } else {

                // create a new collection
                $this->aragonDb->collection()->setName('aneel_proinfa');
                $this->aragonDb->documment()->set('proInfa', $resultados);
                $id = $this->aragonDb->collectionHandler()->create($this->aragonDb->collection());
            }
        }
        catch (ArangoConnectException $e) {
            print 'Connection error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoClientException $e) {
            print 'Client error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoServerException $e) {
            print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
        }

        return response()->json(
            [
                'site' => 'www.biblioteca.aneel.gov.br',
                'palavra_chave' => 'proinfa',
                'responsabilidade' => 'O crawler pega todos os registros do site e realiza os downloads dos mesmos!',
                'status' => 'Crawler Aneel realizado com sucesso!'
            ]
        );

    }
    public function contaDesenvEnerg()
    {


        $response = Curl::to('http://biblioteca.aneel.gov.br/asp/resultadoFrame.asp?modo_busca=legislacao&content=resultado&iBanner=0&iEscondeMenu=0&iSomenteLegislacao=0&iIdioma=0&BuscaSrv=1')
            ->withData(
                array(
                    'leg_campo1' => 'conta desenvolvimento energetico' ,
                    'leg_ordenacao' => 'publicacaoDESC',
                    'leg_normas' => '2',
                    'submeteu' => 'legislacao'
                ) )
            ->post();

        preg_match_all('/td_grid_ficha_background...(.*)/', $this->regexAneel->convert_str($response), $matches);

        $material = $this->regexAneel->capturaMaterial($matches[0][0]);
        $norma =   $this->regexAneel->capturaNorma($matches[0][0]);
        $data_de_assinatura = $this->regexAneel->capturaDataAssinatura($matches[0][0]);
        $data_de_publicacao = $this->regexAneel->capturaDataPublicacao($matches[0][0]);
        $ementa = $this->regexAneel->capturaEmenta($matches[0][0]);
        $orgao_de_origem = $this->regexAneel->capturaOrgaoDeOriem($matches[0][0]);
        $esfera = $this->regexAneel->capturaEsfera($matches[0][0]);
        $situacao = $this->regexAneel->capturaSituacao($matches[0][0]);
        $voto = $this->regexAneel->capturaVoto($matches[0][0]);
        $texto_integral = $this->regexAneel->capturaTextoIntegral($matches[0][0]); // download
        $nota_tecnica = $this->regexAneel->capturaNotaTecnica($matches[0][0]);

        $download_texto_integral = [];
        $download_voto = [];
        $download_nota_tecnica = [];

        foreach ($norma as $key => $item ) {

            if(isset($texto_integral[$key]['texto_integral'])) {
                $results_download_integral = Curl::to($texto_integral[$key]['texto_integral'])
                    ->withContentType('application/pdf')
                    ->download('');
                $download_texto_integral = $this->storageDirectory->saveDirectory('aneel/texto_integral', $texto_integral[$key]['name_arquivo'], $results_download_integral);
            }else{
                $download_texto_integral = array_merge($download_texto_integral,['vazio']);
            }
            if(isset($voto[$key]['voto'])) {
                $results_download_voto = Curl::to($voto[$key]['voto'])
                    ->withContentType('application/pdf')
                    ->download('');
                $download_voto = $this->storageDirectory->saveDirectory('aneel/voto', $voto[$key]['name_arquivo'], $results_download_voto);
            }else{
                $download_voto = array_merge($download_voto,['vazio']);
            }
            if(isset($nota_tecnica[$key]['nota_tecnica'])) {
                $results_download_nota_tecnica = Curl::to($nota_tecnica[$key]['nota_tecnica'])
                    ->withContentType('application/pdf')
                    ->download('');
                $download_nota_tecnica = $this->storageDirectory->saveDirectory('aneel/nota_tecnica', $nota_tecnica[$key]['name_arquivo'], $results_download_nota_tecnica);
            }else{
                $download_nota_tecnica = array_merge($download_nota_tecnica,['vazio']);
            }
        }
        foreach ($material as $key => $item)
        {
            $results[$key] = [
                'url_arquivo_nota_tecnica' => $download_nota_tecnica[$key],
                'url_arquivo_voto' => $download_voto[$key],
                'url_arquivo_texto_integral' => $download_texto_integral[$key]
            ];
            $resultados[] = array_merge($material[$key],$norma[$key],$data_de_assinatura[$key],$data_de_publicacao[$key],$ementa[$key],$orgao_de_origem[$key],$esfera[$key],$situacao[$key],$results[$key]);
        }
        // ------------------------------------------------------------------------Crud--------------------------------------------------------------------------------------------------

        try {
            if ($this->aragonDb->collectionHandler()->has('aneel_proinfa')) {

                $this->aragonDb->documment()->set('conta_desenvolvimento_energetico', $resultados);
                $id = $this->aragonDb->documentHandler()->save('aneel_proinfa', $this->aragonDb->documment());

            } else {
                // create a new collection
                $this->aragonDb->collection()->setName('aneel_proinfa');
                $this->aragonDb->documment()->set('conta_desenvolvimento_energetico', $resultados);
                $id = $this->aragonDb->collectionHandler()->create($this->aragonDb->collection());
            }
        }
        catch (ArangoConnectException $e) {
            print 'Connection error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoClientException $e) {
            print 'Client error: ' . $e->getMessage() . PHP_EOL;
        } catch (ArangoServerException $e) {
            print 'Server error: ' . $e->getServerCode() . ':' . $e->getServerMessage() . ' ' . $e->getMessage() . PHP_EOL;
        }

        return response()->json(
            [
                'site' => 'www.biblioteca.aneel.gov.br',
                'palavra_chave' => 'conta desenvolvimento energetico',
                'responsabilidade' => 'O crawler pega todos os registros do site e realiza os downloads dos mesmos!',
                'status' => 'Crawler Aneel realizado com sucesso!'
            ]
        );




    }


}
