<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02/05/18
 * Time: 16:57
 */

namespace Crawler\Regex;


class RegexOns extends AbstractRegex
{

    /** Metodo getAcervoDigitalPmoSemanal */

    public function capturaRequestDigest($page_acesso)
    {
        $regex = '/__REQUESTDIGEST" value="(.*?)"/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function getUrlDownload($page_acesso)
    {
        $regex = '/.FileRef.:.(.*).."FileDirRef/';
        $results = $this->regexFirst($regex, $page_acesso, 0);
        $tratativa = $this->pregReplaceString('u002f','',$results);

        return $this->pregReplaceString('\\','/',$tratativa);
    }
    public function getNameDownload($page_acesso)
    {
        $regex = '/es\/(.*)/';
        return $results = $this->regexFirst($regex, $page_acesso, 0);

    }
    public function testString($page_acesso)
    {
       $result = $this->pregReplaceString('src="','src="https://tableau.ons.org.br',$page_acesso);
      return $results = $this->pregReplaceString('href="','src="https://tableau.ons.org.br',$result);

    }
}