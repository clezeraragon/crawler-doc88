<?php

namespace Crawler\Model;


use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\CollectionHandler as CollectionHandler;
use ArangoDBClient\Collection as ArangoCollection;
use ArangoDBClient\Document as ArangoDocument;
use ArangoDBClient\DocumentHandler as ArangoDocumentHandler;

class ArangoDb
{

    private $arangoConnection;
    private $collectionHandler;
    private $collection;
    private $documentHandler;
    private $document;

    public function __construct()
    {
        $this->arangoConnection = new ArangoConnection(config('arangodb.arango'));
        $this->collectionHandler = new CollectionHandler($this->arangoConnection);
        $this->documentHandler = new ArangoDocumentHandler($this->arangoConnection);
        $this->document = new ArangoDocument();
        $this->collection =  new ArangoCollection();
    }
    /** Conexao com ArangoDb */
    public function connectionArango()
    {
        return $this->arangoConnection;
    }
    /** Manipulador de Collation */
    public function collectionHandler()
    {
        return $this->collectionHandler;
    }
    /** metodo para criar crud de Collation */
    public function collection()
    {
        return  $this->collection;
    }
    /** Manipulador e documentos */
    public function documentHandler()
    {
        return  $this->documentHandler;
    }
    /** Metodo para criar crud de Document */
    public function documment()
    {
        return $this->document;
    }


}
