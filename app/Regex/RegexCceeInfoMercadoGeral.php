<?php
/**
 * Created by PhpStorm.
 * User: Clezer Aragon Ramos
 * Date: 25/04/18
 * Time: 16:55
 */

namespace Crawler\Regex;


class RegexCceeInfoMercadoGeral extends AbstractRegex
{
    public function capturaUrlDownloadGeral($page_acesso)
    {
        $regex = '/class="btn-enviar-big..href="\/(.*?)"/ ';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function capturaUrlDownloadIndividual($page_acesso)
    {
        $regex = '/btn-enviar-big margin-left10..href="\/(.*?)"/ ';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
}