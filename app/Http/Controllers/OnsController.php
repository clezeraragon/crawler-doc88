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

    public function getAcervoDigitalIpdoDiario()
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

    public function getAcervoDigitalPmoSemanal()
    {

        $data_raw = [
            'raw data' => '<Request xmlns="http://schemas.microsoft.com/sharepoint/clientquery/2009" SchemaVersion="15.0.0.0" LibraryVersion="16.0.0.0" ApplicationName="Javascript Library"><Actions><ObjectPath Id="1" ObjectPathId="0" /><ObjectPath Id="3" ObjectPathId="2" /><ObjectPath Id="5" ObjectPathId="4" /><ObjectPath Id="7" ObjectPathId="6" /><ObjectIdentityQuery Id="8" ObjectPathId="6" /><ObjectPath Id="10" ObjectPathId="9" /><Query Id="11" ObjectPathId="9"><Query SelectAllProperties="true"><Properties /></Query><ChildItemQuery SelectAllProperties="true"><Properties /></ChildItemQuery></Query></Actions><ObjectPaths><StaticProperty Id="0" TypeId="{3747adcd-a3c3-41b9-bfab-4a64dd2f1e0a}" Name="Current" /><Property Id="2" ParentId="0" Name="Web" /><Property Id="4" ParentId="2" Name="Lists" /><Method Id="6" ParentId="4" Name="GetByTitle"><Parameters><Parameter Type="String">Home - Webdoor</Parameter></Parameters></Method><Method Id="9" ParentId="6" Name="GetItems"><Parameters><Parameter TypeId="{3d248d7b-fc86-40a3-aa97-02a75d69fb8a}"><Property Name="DatesInUtc" Type="Boolean">true</Property><Property Name="FolderServerRelativeUrl" Type="Null" /><Property Name="ListItemCollectionPosition" Type="Null" /><Property Name="ViewXml" Type="String">&lt;View&gt; &lt;Query&gt; &lt;Where&gt;	&lt;And&gt;	&lt;Leq&gt;	&lt;FieldRef Name=\'PublicarEm\' /&gt;	&lt;Value IncludeTimeValue=\'TRUE\' Type=\'DateTime\'&gt;2018-04-25T10:01:04Z&lt;/Value&gt;	&lt;/Leq&gt; &lt;Or&gt;	&lt;Geq&gt; &lt;FieldRef Name=\'ExpirarEm\' /&gt;	&lt;Value IncludeTimeValue=\'TRUE\' Type=\'DateTime\'&gt;2018-04-25T10:01:04Z&lt;/Value&gt;	&lt;/Geq&gt; &lt;IsNull&gt; &lt;FieldRef Name=\'ExpirarEm\' /&gt; &lt;/IsNull&gt; &lt;/Or&gt; &lt;/And&gt; &lt;/Where&gt; &lt;OrderBy&gt; &lt;FieldRef Name=\'Ordem\' /&gt; &lt;FieldRef Name=\'PublicarEm\' Ascending=\'desc\' /&gt; &lt;/OrderBy&gt; &lt;/Query&gt; &lt;RowLimit&gt;1&lt;/RowLimit&gt;&lt;/View&gt;</Property></Parameter></Parameters></Method></ObjectPaths></Request>'
        ];

        $headers = ['X-Requested-With' => 'XMLHttpRequest','Content-Type' => 'text/xml','X-RequestDigest'=>'0xC93DE16C7303A566966C41142C1D109583CA86D39BD8DFACC45103D6B5102600FAA6688ABF148F9721056000E4F7B7A84AFE62D9FCC5A07EE377C7C962223C2C,25 Apr 2018 13:01:03 -0000'];
        $body = 'Hello!';
        $request = new \Request('HEAD', 'http://ons.org.br', $headers);

        $jar = new \GuzzleHttp\Cookie\CookieJar;
        $crawler = $this->client->request('POST', 'http://ons.org.br/_vti_bin/client.svc/ProcessQuery',[
            'cookies' => $jar,
            'body' => $data_raw
        ]);
        $get_response_site = $this->client->getResponse();

        dump($get_response_site);
    }

}
//http://ons.org.br/AcervoDigitalDocumentosEPublicacoes/SUMARIO_EXECUTIVO_PMO_201804_RV3.pdf
//http://ons.org.br/AcervoDigitalDocumentosEPublicacoes/InformePMO_ABR2018_RV3.pdf
//
//http://ons.org.br/_layouts/download.aspx?SourceUrl=http://ons.org.br/AcervoDigitalDocumentosEPublicacoes/SUMARIO_EXECUTIVO_PMO_201804_RV3.pdf