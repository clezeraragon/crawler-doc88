<?php

use ArangoDBClient\ConnectionOptions as ArangoConnectionOptions;
use ArangoDBClient\UpdatePolicy as ArangoUpdatePolicy;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'arango' => [
        // database name
        ArangoConnectionOptions::OPTION_DATABASE => env('OPTION_DATABASE', '_system'),
        // server endpoint to connect to
        ArangoConnectionOptions::OPTION_ENDPOINT => env('OPTION_ENDPOINT', 'tcp://127.0.0.1:8529'),
        // authorization type to use (currently supported: 'Basic')
        ArangoConnectionOptions::OPTION_AUTH_TYPE => env('OPTION_AUTH_TYPE', 'Basic'),
        // user for basic authorization
        ArangoConnectionOptions::OPTION_AUTH_USER => env('OPTION_AUTH_USER', 'root'),
        // password for basic authorization
        ArangoConnectionOptions::OPTION_AUTH_PASSWD => env('OPTION_AUTH_PASSWD', ''),
        // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
        ArangoConnectionOptions::OPTION_CONNECTION => env('OPTION_CONNECTION', 'Keep-Alive'),
        // connect timeout in seconds
        ArangoConnectionOptions::OPTION_TIMEOUT => env('OPTION_TIMEOUT', '3'),
        // whether or not to reconnect when a keep-alive connection has timed out on server
        ArangoConnectionOptions::OPTION_RECONNECT => env('OPTION_RECONNECT', 'true'),
        // optionally create new collections when inserting documents
        ArangoConnectionOptions::OPTION_CREATE => env('OPTION_CREATE', 'true'),
        // optionally create new collections when inserting documents
        ArangoConnectionOptions::OPTION_UPDATE_POLICY => ArangoUpdatePolicy::LAST,
    ]
];
