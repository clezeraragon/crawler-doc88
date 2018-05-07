<?php
/**
 * Created by PhpStorm.
 * User: clezer Aragon Ramos
 * Date: 10/04/18
 * Time: 11:06
 */

namespace Crawler\Regex;
use DateTime;

abstract class AbstractRegex
{
    protected function regexFirst($regex, $page, $det = 0) {
        $matches = array();
        preg_match($regex, $page, $matches);
        if ($det) {
            dump($matches);
        }

        return $this->formataArrayPregMatch($matches, $det);
    }

    protected function regexAll($regex, $page, $det = 0, $keys) {
        $matches = array();
        preg_match_all($regex, $page, $matches);
        if ($det) {
            dump($matches);
        }
        return $this->formataArrayPregMatchAll($matches, $keys);
    }

    /*
     * Metodo para formatar array criado pela função preg_match_all
     */

    protected function formataArrayPregMatchAll($array, $key = array(0)) {
        $array = array_slice($array, 1); //primeira posição do preg_match_all é a string que foi aplicada a regra
        $pregoes = array();
        for ($i = 0; $i <= count($array[0]) - 1; $i++) { // -1 par ajustar posições do array por ele começar no num 0
            foreach ($key as $k => $value) {
                $pregoes[$i][$value] = $this->convert_str($array[$k][$i]);
            }
        }
        return $pregoes;
    }

    /*
     * Metodo para formatar array criado pela função preg_match
     */

    protected function formataArrayPregMatch($array, $det = 0) {
        if ($det) {
            dump($array);
        }

        if (isset($array[1])) {
            return $this->convert_str($array[1]);
        } else {
            unset($array);
            return null;
        }
    }

    protected function limpaString($string) {
        $string = trim($string);
        return $string;
    }

    protected function formataDataHoraISO($dtHr) {
        //11/10/2012 16:41
        //echo $dtHr;
        $newData = substr($dtHr, 6, 4) . "-" .
            substr($dtHr, 3, 2) . "-" .
            substr($dtHr, 0, 2);
        if (substr($dtHr, 11, 2)) {
            $newData .= " " . substr($dtHr, 11, 2) . ":" . substr($dtHr, 14, 2) . ":00";
        }

        return $newData;
    }

    public function formataDataHora_Padrao_ISO($data_hora) {

        $data = NULL;

        $date = DateTime::createFromFormat('d/m/Y H:i', $data_hora);

        if ($date) {
            $data = $date->format('Y-m-d H:i:s'); # Formata a hora para padrão ISO
        }

        return $data;
    }
    public function formataDataISO($data_hora) {

        $data = NULL;

        $date = DateTime::createFromFormat('d/m/Y', $data_hora);

        if ($date) {
            $data = $date->format('Y-m-d');
        }

        return $data;
    }
    public function formataDataBr($data_hora) {

        $data = NULL;

        $date = DateTime::createFromFormat('d/m/Y', $data_hora);

        if ($date) {
            $data = $date->format('d-m-Y');
        }

        return $data;
    }

    public function formataValor($val) {
        //25.305.480,1600
        $val = preg_replace('/\./', '', $val);
        $val = preg_replace('/,/', '.', $val);

        return $val;
    }
    public function pregReplaceString($objetivo,$replace,$valor) {
        //25.305.480,1600
        $results = str_replace($objetivo,$replace,$valor);


        return $results;
    }

    public function formataQuantidade($qtd) {
        return preg_replace('/\./', '', $qtd);
    }

    public function formataBoolean($string) {
        return preg_replace('/\./', '', $string);
    }

    public function formataDataDecimal($dt) {
        //13/08/2012 09:36:22:757

        $newData = substr($dt, 6, 4) . substr($dt, 3, 2) . substr($dt, 0, 2) . substr($dt, 11, 2) .
            substr($dt, 14, 2) . substr($dt, 17, 2) . "." . substr($dt, 20, 3);

        return $newData;
    }

    function convert_str($str) {
        // remover o excesso whitespace
        // procura por um um ou mais espaços e substitui-los todos com um único espaço.
        $str = preg_replace('/ +/', ' ', $str);
        // verificar se há casos de mais de duas quebras de linha em uma fileira
        // e, em seguida, alterá-los para um total de duas quebras de linha
        $str = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\r\n\r\n", $str);
        //se existir, remova \ r \ n \ r \ n no início
        $str = preg_replace('/^(\r\n\r\n)/', '', $str);

        //se existir, remova \ r \ n \ r \ n no final
        $str = preg_replace('/$(\r\n\r\n)/', '', $str);

        //se existir, remover um caractere de espaço antes de qualquer \ r \ n
        $str = str_replace(" \r\n", "\r\n", $str);
        //se existir, remover um caractere de espaço logo após qualquer \ r \ n
        $str = str_replace("\r\n ", "\r\n", $str);
        // se existe, remover um caractere de espaço, pouco antes das pontuações abaixo:
        #$punc = array('.',',',';',':','...','?','!','-','—','/','\\','“','”','‘','’','"','\'','(',')','[',']','’','{','}','*','&','#','^','<','>','|');
        $punc = array(' .', ' ,', ' ;', ' :', ' ...', ' ?', ' !', ' -', ' —', ' /', ' \\', ' “', ' ”', ' ‘', ' ’', ' "', ' \'', ' (', ' )', ' [', ' ]', ' ’', ' {', ' }', ' *', ' &', ' #', ' ^', ' <', ' >', ' |');
        $replace = array('.', ',', ';', ':', '...', '?', '!', '-', '—', '/', '\\', '“', '”', '‘', '’', '"', '\'', '(', ')', '[', ']', '’', '{', '}', '*', '&', '#', '^', '<', '>', '|');
        $str = str_replace($punc, $replace, $str);

        $str = $string = preg_replace('/\s(?=\s)/', '', $str);
        $string = preg_replace('/[\n\r\t]/', ' ', $str);

        return $string;
    }

}