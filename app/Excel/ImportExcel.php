<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09/05/18
 * Time: 13:57
 */

namespace Crawler\Excel;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;

class ImportExcel
{
    private $excel;
    private $startRow;

    // Dados InfoMercad

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function setConfigStartRow($row)
    {
        return $this->startRow = config(['excel.import.startRow'=> $row]);
    }

    public function consumoAneel($file, $sheet, $startRow, $takeRows, $date)
    {
        $data = [];
        $oldSubmercado = '';
        $oldSemana = '';
        $oldPatamar = '';
        $year = $date->year;
        $this->setConfigStartRow($startRow);
        $months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $daysInMonths = [
            'Janeiro' => 31,
            'Fevereiro' => Carbon::createFromFormat('m/Y', '02/' . $year)->daysInMonth,
            'Março' => 31,
            'Abril' => 30,
            'Maio' => 31,
            'Junho' => 30,
            'Julho' => 31,
            'Agosto' => 31,
            'Setembro' => 30,
            'Outubro' => 31,
            'Novembro' => 30,
            'Dezembro' => 31
        ];

        \Excel::selectSheetsByIndex($sheet)
            ->load($file, function($reader) use ($takeRows) {
                $reader->takeRows($takeRows);
            })
            ->get()
            ->each(function($i, $k) use (&$data, &$oldSubmercado, &$oldSemana, &$oldPatamar, &$date, $months, $daysInMonths) {
                $rowData = $i->all();
                if (
                    $k === 0 &&
                    (
                        empty($rowData['submercado']) ||
                        empty($rowData['no_semana']) ||
                        empty($rowData['patamar'])
                    )
                ) {
                    throw new \Exception('O primeiro item não pode estar com o submercado, semana ou patamar vazio');
                } else {
                    unset($rowData[0]);
                    $submercado = ! empty($rowData['submercado']) ? $rowData['submercado'] : $oldSubmercado;
                    $semana = ! empty($rowData['no_semana']) ? $rowData['no_semana'] : $oldSemana;
                    $patamar = ! empty($rowData['patamar']) ? $rowData['patamar'] : $oldPatamar;
                    unset($rowData['submercado']);
                    unset($rowData['no_semana']);
                    unset($rowData['patamar']);

                    $arr = array_combine($months, $rowData);
                    $arrPatamar = [];
                    array_walk($arr, function($value, $key) use ($date, $daysInMonths, &$arrPatamar) {
                        $total = $value;
                        if (! is_null($value)) {
                            $total_round = round($value * 24 * $daysInMonths[$key], 3);
                            $total = number_format($total_round,3,",",".");

                        }

                        $arrPatamar[$key] = $total;
                    });

                    if (isset($data[$submercado][$semana])) {
                        $data[$submercado][$semana][$patamar] = $arrPatamar;
                    } elseif (isset($data[$submercado])) {
                        $data[$submercado][$semana] = [
                            $patamar => $arrPatamar
                        ];
                    } else {
                        $data[$submercado] = [
                            $semana => [
                                $patamar => $arrPatamar
                            ]
                        ];
                    }

                    $oldSubmercado = $submercado;
                    $oldSemana = $semana;
                    $oldPatamar = $patamar;
                }
            });

        return $data;
    }
}