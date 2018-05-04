<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04/05/18
 * Time: 10:45
 */

namespace Crawler\Regex;


class RegexEpe extends AbstractRegex
{
    public function capturaDownload($page_acesso)
    {
        $regex = '/<li\><a.href="(.*).xls/';
        return $this->regexFirst($regex, $this->convert_str($page_acesso), 0);
    }

}