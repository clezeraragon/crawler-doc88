<?php

namespace Crawler\Http\Controllers;

use Illuminate\Http\Request;
use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\CollectionHandler as CollectionHandler;
use ArangoDBClient\Collection as ArangoCollection;
use ArangoDBClient\Document as ArangoDocument;
use ArangoDBClient\DocumentHandler as ArangoDocumentHandler;
use Crawler\Model\AragonDb;

class ArangoDbController extends Controller
{

    private $aragonDb;

    public function __construct(AragonDb $aragonDb)
    {
        $this->aragonDb = $aragonDb;
    }

    public function index()
    {


        if ($this->aragonDb->collectionHandler()->has('leandro')) {


            // use set method to set document properties
            $this->aragonDb->documment()->set('name', 'Leandro');
            $this->aragonDb->documment()->set('age', 25);
            $this->aragonDb->documment()->set('status', 'ativo');

            // use magic methods to set document properties
            $this->aragonDb->documment()->leandro = ['festa', 'esporte', 'natação'];

            // send the document to the server
            $id =  $this->aragonDb->documentHandler()->save('leandro', $this->aragonDb->documment());

            // check if a document exists
            $result =  $this->aragonDb->documentHandler()->has('leandro', $id);
            var_dump($result);

            // print the document id created by the server
            var_dump($id);
            var_dump($this->aragonDb->documment()->getId());
        } else {

                // create a new collection

                $this->aragonDb->collection()->setName('leandro');
                $id = $this->aragonDb->collectionHandler()->create($this->aragonDb->collection());
         }


    }
}
