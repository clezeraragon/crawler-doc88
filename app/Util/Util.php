<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 30/04/18
 * Time: 14:26
 */

namespace Crawler\Util;


use Carbon\Carbon;

class Util
{

    public static function getDateIso()
    {
        $date = Carbon::now();
        return $date->format('Y-m-d');
    }

    public static function getDateBrSubDays($format,$day)
    {
        if($format == 'br')
        {
            $date = Carbon::now()->subDay($day);
            return $date->format('d-m-Y');
        }
        if($format == 'us')
        {
            $date = Carbon::now()->subDay($day);
            return $date->format('Y-m-d');
        }

    }
    public static function getMesAno($mes_ano)
    {
      $date = Carbon::createFromFormat('m/Y',$mes_ano);

      return $date->format('Y-m');
    }

}