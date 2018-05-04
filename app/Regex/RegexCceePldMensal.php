<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04/05/18
 * Time: 10:09
 */

namespace Crawler\Regex;


class RegexCceePldMensal extends AbstractRegex
{

    public function clearHtml($page_acesso)
    {
        return $this->convert_str($page_acesso);
    }
    public function capturaMes($page_acesso)
    {
        $regex = '/class="linebt".[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function capturaSeCo($page_acesso)
    {
        $regex = '/class="linebt".[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function capturaS($page_acesso)
    {
        $regex = '/class="linebt".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function capturaNe($page_acesso)
    {
        $regex = '/class="linebt".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function capturaN($page_acesso)
    {
        $regex = '/class="linebt".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
}