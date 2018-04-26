<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25/04/18
 * Time: 17:56
 */

namespace Crawler\Regex;


class RegexCceeInfoMercadoIndividual
{

    public function capturaUrlDownload($page_acesso)
    {
        $regex = '/class="btn-enviar-big margin-left10..href="\/(.*?)"/ ';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
}