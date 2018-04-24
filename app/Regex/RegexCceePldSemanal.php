<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/04/18
 * Time: 11:40
 */

namespace Crawler\Regex;


class RegexCceePldSemanal extends AbstractRegex
{

    /// -------------------------------------------------- Sudeste/Centro-Oeste -----------------------------------------------------------------------//

    public function clearHtml($page_acesso)
    {
       return $this->convert_str($page_acesso);
    }
    public function getSemana($page_acesso)
    {
        $regex = '/Semana.(.*?)-/';
        return $this->regexFirst($regex, $page_acesso, 0);
    }
    public function getPeriodoDe($page_acesso)
    {
        $regex = '/Período:.(.*?) /';
        return $this->formataDataISO($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getPeriodoAte($page_acesso)
    {
        $regex = '/Período:..............(.*?) /';
        return $this->formataDataISO($this->regexFirst($regex, $page_acesso, 0));
    }

    public function getSudesteCentroOestePesada($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getSudesteCentroOesteMedia($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getSudesteCentroOesteLeve($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }

    /// -------------------------------------------------- Sul -----------------------------------------------------------------------//

    public function getSulPesada($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getSulMedia($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getSulLeve($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }

    /// -------------------------------------------------- Nordeste -----------------------------------------------------------------------//

    public function getNordestePesada($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getNordesteMedia($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getNordesteLeve($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }

    /// -------------------------------------------------- Norte -----------------------------------------------------------------------//

    public function getNortePesada($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getNorteMedia($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
    public function getNorteLeve($page_acesso)
    {
        $regex = '/class="displayTag-even".[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->limpaString($this->regexFirst($regex, $page_acesso, 0));
    }
}