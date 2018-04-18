<?php

namespace Crawler\Http\Controllers;


use Crawler\Regex\RegexAneel;
use Crawler\StorageDirectory\StorageDirectory;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class AneelController extends Controller
{

    private $regexAneel;
    private $storageDirectory;

    public function __construct( RegexAneel $regexAneel,StorageDirectory $storageDirectory)
    {
        $this->regexAneel = $regexAneel;
        $this->storageDirectory = $storageDirectory;
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

                  $results_download_integral =  Curl::to($texto_integral[0]['texto_integral'])
                        ->withContentType('application/pdf')
                        ->download('');
                  $this->storageDirectory->saveDirectory('aneel/texto_integral',$texto_integral[0]['name_arquivo'],$results_download_integral);

                  $results_download_voto =  Curl::to($voto[0]['voto'])
                        ->withContentType('application/pdf')
                        ->download('');
                  $this->storageDirectory->saveDirectory('aneel/voto',$voto[0]['name_arquivo'],$results_download_voto);

                  $results_download_nota_tecnica =  Curl::to($nota_tecnica[0]['nota_tecnica'])
                        ->withContentType('application/pdf')
                        ->download('');
                    $this->storageDirectory->saveDirectory('aneel/nota_tecnica',$nota_tecnica[0]['name_arquivo'],$results_download_nota_tecnica);

                              dump($norma);

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

        $results_download_integral =  Curl::to($texto_integral[0]['texto_integral'])
            ->withContentType('application/pdf')
            ->download('');
        $this->storageDirectory->saveDirectory('aneel/texto_integral',$texto_integral[0]['name_arquivo'],$results_download_integral);

        $results_download_voto =  Curl::to($voto[0]['voto'])
            ->withContentType('application/pdf')
            ->download('');
        $this->storageDirectory->saveDirectory('aneel/voto',$voto[0]['name_arquivo'],$results_download_voto);

        $results_download_nota_tecnica =  Curl::to($nota_tecnica[0]['nota_tecnica'])
            ->withContentType('application/pdf')
            ->download('');
        $this->storageDirectory->saveDirectory('aneel/nota_tecnica',$nota_tecnica[0]['name_arquivo'],$results_download_nota_tecnica);

//        dump($nota_tecnica);

    }


}
