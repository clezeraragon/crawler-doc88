<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 07/05/18
 * Time: 14:03
 */

namespace Crawler\Regex;


class RegexEletrobras extends AbstractRegex
{

    public function capturaUrlMovimentacao($page_acesso)
    {
        $regex = '/_layouts\/15\/images\/icxls\.png[^>]+>[^>]+>[^>]+>[^>]+href..(.*?)"/';
        $result =  $this->regexFirst($regex, $page_acesso);
        $result_1 = $this->pregReplaceString(['- ',' - '],' - ',$result);
        $result_2 = urlencode($result_1);
        $result_3 = $this->pregReplaceString('+','%20',$result_2);
        return $this->pregReplaceString('%2F','/',$result_3);


    }
}