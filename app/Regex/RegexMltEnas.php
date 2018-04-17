<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16/04/18
 * Time: 17:03
 */

namespace Crawler\Regex;


class RegexMltEnas extends AbstractRegex
{
   public function capturaDowloadMltEnas($page_acesso)
   {
       $regex = '/<a href="..\/(.*?)"/';
       return $this->regexFirst($regex, $page_acesso, 0);
   }
   public function capturaNameArquivo($page_acesso)
   {
       $regex = '/([0-9].*)/';
       return $this->regexFirst($regex, $page_acesso, 0);
   }
}