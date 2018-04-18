<?php

namespace Crawler\Http\Controllers;

use Illuminate\Http\Request;
use ArangoDBClient\Connection as ArangoConnection;


class ArangoDbController extends Controller
{

    private $arangoConnection;

    public function __construct()
    {
        $this->arangoConnection = new ArangoConnection(config('arangodb.arango'));
    }

    public function index()
    {


        dump($this->arangoConnection);
//       return \config('arangodb.conarangodb');
    }
}
