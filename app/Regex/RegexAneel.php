<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/04/18
 * Time: 18:17
 */

namespace Crawler\Regex;


class RegexAneel extends AbstractRegex
{

    public function capturaNorma($page_acesso)
    {
        $regex = '/\>Norma[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['norma']);
    }

    public function capturaMaterial($page_acesso)
    {
        $regex = '/\>Material[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['material']);
    }
    public function capturaDataAssinatura($page_acesso)
    {
        $regex = '/\>Data de assinatura[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['Data_assinatura']);
    }
    public function capturaDataPublicacao($page_acesso)
    {
        $regex = '/\>Data de publica...[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['Data_publicacao']);
    }
    public function capturaEmenta($page_acesso)
    {
        $regex = '/\>Ementa[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['Ementa']);
    }
    public function capturaOrgaoDeOriem($page_acesso)
    {
        $regex = '/\>Órgão de origem[^>]+>[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['orgao_de_origem']);
    }
    public function capturaEsfera($page_acesso)
    {
        $regex = '/\>Esfera[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['esfera']);
    }
    public function capturaSituacao($page_acesso)
    {
        $regex = '/\>Situa.[^>]+>[^>]+>([^<]+)+</';
        return $this->regexAll($regex, $page_acesso, 0,['situacao']);
    }
    public function capturaTextoIntegral($page_acesso)
    {
        $regex = '/\>Texto Integral[^>]+>[^>]+>[^>]+>([^<]+)+</';
        $results = $this->regexAll($regex, $page_acesso, 0,['texto_integral']);

        foreach ($results as $key => $result)
        {
            $name_arquivo = preg_replace('/http.(.*)\//', ' ', $result);
            $resultados[$key] = [
                'texto_integral' => $result['texto_integral'],
                'name_arquivo' => trim($name_arquivo['texto_integral'])
            ];
        }
        return $resultados;
    }
    public function capturaVoto($page_acesso)
    {
        $regex = '/\>Voto[^>]+>[^>]+>[^>]+>([^<]+)+</';
        $results = $this->regexAll($regex, $page_acesso, 0,['voto']);

        foreach ($results as $key => $result)
        {
            $name_arquivo = preg_replace('/http.(.*)\//', ' ', $result);
            $resultados[$key] = [
                'voto' => $result['voto'],
                'name_arquivo' => trim($name_arquivo['voto'])
            ];
        }
        return $resultados;
    }
    public function capturaNotaTecnica($page_acesso)
    {
        $regex = '/\>Nota Técnica.[^>]+>[^>]+>[^>]+>([^<]+)+</';
        $results = $this->regexAll($regex, $page_acesso, 0,['nota_tecnica']);

        foreach ($results as $key => $result)
        {
            $name_arquivo = preg_replace('/http.(.*)\//', ' ', $result);
            $resultados[$key] = [
                'nota_tecnica' => $result['nota_tecnica'],
                'name_arquivo' => trim($name_arquivo['nota_tecnica'])
            ];
        }
        return $resultados;
    }
    function tratarImput($dado)
    {
        return str_replace(" ", "+", $dado);
    }
    // Pegar audiência
    public function capturaAudiencia($page_acesso)
    {
        $regex = '/href..(.*)"\>/';
        return $this->regexFirst($regex, $page_acesso);
    }

}