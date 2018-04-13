<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/04/18
 * Time: 16:23
 */

namespace Crawler\Regex;


class RegexSdroSemanal extends AbstractRegex
{
    public function capturaUrlAtual($page_acesso)
    {
        $regex = '/"atual".src="(.*?)"/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function capturaUrlData($page_acesso)
    {
        $regex = '/([0-9].*)\//';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function capturaUrlDownloadExcel($page_acesso)
    {
        $regex = '/id=.xls-link..href=..(.*?)"/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function capturaUrlDownloadName($page_acesso)
    {
        $regex = '/\/Html.(.*)/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
}