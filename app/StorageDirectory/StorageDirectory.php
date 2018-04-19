<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/04/18
 * Time: 14:55
 */

namespace Crawler\StorageDirectory;
use Storage;

class StorageDirectory
{

    public function saveDirectory($diretorio,$arquivo,$content)
    {
       Storage::disk('local')->put($diretorio.'/'.$arquivo, $content);
      return Storage::files($diretorio);
    }
}