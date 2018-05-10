<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09/05/18
 * Time: 13:57
 */

namespace Crawler\Excel;

use Maatwebsite\Excel\Excel;

class ImportExcel
{
    private $excel;
    private $startRow;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function setConfigStartRow($row)
    {
        return $this->startRow = config(['excel.import.startRow'=> $row]);
    }

    public function consumoAneel($objeto)
    {
//      $results =  $this->excel->load($objeto)->get();

//        $results = $this->excel->selectSheets('003 Consumo')->load($objeto)->get
        $this->setConfigStartRow(2);
      $teste =  \Excel::selectSheetsByIndex(0)->load($objeto, function($reader) {
//           $reader->skipColumns(1);
//           $reader->get();

        })->get();

      dump($teste);
         die;

         dump($results->skipRows(14)->takeRows(73)->skipColumns(1)->takeColumns(15));die;

        foreach ($results as $key => $result)
        {

//            if($result->getTitle() == '003 Consumo')
//            {
//              $result->each(function ($row)
//              {
                 dump($result);
//              });
//            }

            {

            }
        }



    }

}