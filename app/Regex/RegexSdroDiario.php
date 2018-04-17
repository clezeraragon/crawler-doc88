<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/04/18
 * Time: 16:23
 */

namespace Crawler\Regex;


class RegexSdroDiario extends AbstractRegex
{
    public function capturaUrlAtual($page_acesso)
    {
        $regex = '/"atual".src="(.*?)"/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function capturaUrlData($page_acesso)
    {
        $regex = '/([0-9].*)\./';
       $result = $this->regexFirst($regex, $page_acesso, 0);
       if(isset($result))
       {
           return $this->formataDataISORegex($result);
       }
    }
    public function capturaUrlDownloadExcel($page_acesso)
    {
        $regex = '/id=.xls-link..href=...(.*?)"/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function capturaUrlDownloadName($page_acesso)
    {
        $regex = '/Html.(.*)/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }

    public function formataDataISORegex($data)
    {
        // retorna = 2018_04_12
        $result = explode('-',$data);
        return $result[2].'_'.$result[1].'_'.$result[0];
    }
}