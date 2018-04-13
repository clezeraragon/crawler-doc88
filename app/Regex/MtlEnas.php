<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13/04/18
 * Time: 13:25
 */

namespace Crawler\Regex;


class MtlEnas extends AbstractRegex
{

    public function capturaUrlDownloadName($page_acesso)
    {
        $regex = '/\>Norma[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['norma']);
    }
}