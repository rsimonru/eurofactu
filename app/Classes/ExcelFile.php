<?php

namespace App\Classes;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelFile {
    var $filename;
    var $sheetname;
    var $filters;
    var $sort;
    var $columns;
    var $model;
    var $records;

    function __construct($model = null, $filters=[], $columns=[], $filename='', $sort=[]) {
        $this->model = ($model) ? new $model():null;
        $this->filters = $filters;
        $this->sort = $sort;
        $this->columns = $columns;
        $this->filename = $filename;
    }

    function generate($table = null){
        // insert $vcTable into $objPHPExcel's Active Sheet through $excelHTMLReader
        $spreadsheet = new Spreadsheet();
        $reader = new Html();
        if (!$table) {
            $this->records = $this->model::emtGet(0, -1, $this->sort, $this->filters);
            $table = view('documents.excel',[
                'columns' => $this->columns,
                'records' => $this->records
            ])->render();
        }

        $reader->loadFromString($table, $spreadsheet);

        // Capturamos la fila y columna máxima del archivo a exportar.
        $iMaxCol=$spreadsheet->getActiveSheet()->getHighestColumn();
        $iMaxFil=$spreadsheet->getActiveSheet()->getHighestRow();

        // Establecemos el nombre de la hoja de calculo.
        $spreadsheet->getActiveSheet()->setTitle('DATOS'); // Change sheet's title if you wanth

        // Establecemos la altura de la primera fila, la de los titulos.
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);

        // Ponemos un autofiltro en la primera fila
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:'.$iMaxCol.$iMaxFil);

        // Establecemos algunos paremetros de estilo de hoja de datos para la primera fila.
        $spreadsheet->getActiveSheet()->getStyle("A1:".$iMaxCol."1")->applyFromArray(
            array(
                'font'  => array(
                    'bold'  => true,
                    'name'  => 'Arial',
                    'size'  =>10,
                    'color' => [ 'rgb' => 'ffffff' ]
                ),
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => Border::BORDER_THIN,
                        'color'=>[ 'rgb' => 'ffffff' ]
                    )
                ),
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => array(
                    'fillType' => Fill::FILL_SOLID,
                    'color' => array('rgb' => '5c9bd1')
                )
            )
        );

        // Establecemos algunos parámetros de estilo de celda para el área de datos.
        $spreadsheet->getActiveSheet()->getStyle("A2:".$iMaxCol.$iMaxFil)->applyFromArray(
            array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => Border::BORDER_THIN,
                        'color'=>[ 'rgb' => 'cccccc' ]
                    )
                ),
                'font'  => array(
                    'bold'  => false,
                    'name'  => 'Arial',
                    'size'  =>10,
                    'color' => [ 'rgb' => '000000' ],
                    'underline' => Font::UNDERLINE_NONE
                ),
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            )
        );

        // recorremos la primera fila columna a columna y establecemos el ancho automatico. Además establecemos el alto de la fila.
        foreach(range('A',$iMaxCol) as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);

            // Recorremos todas las celdas para aplicar el formato moneda
            for ($rowID = 1; $rowID <= $iMaxFil; $rowID++) {
                $value = $spreadsheet->getActiveSheet()->getCell($columnID.$rowID)->getValue();
                if(strpos($value,"€")){
                    $dValue = trim(str_replace("€","",$value));
                    if (is_numeric($dValue)) {
                        $spreadsheet->getActiveSheet()->setCellValue($columnID.$rowID,trim($dValue));
                        $spreadsheet->getActiveSheet()->getStyle($columnID.$rowID)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $spreadsheet->getActiveSheet()->getStyle($columnID.$rowID)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
                    }
                }
                if(strpos($value,"%")){
                    $dValue = trim(str_replace("%","",$value));
                    if (is_numeric($dValue)) {
                        $spreadsheet->getActiveSheet()->setCellValue($columnID.$rowID,trim($dValue)/100);
                        $spreadsheet->getActiveSheet()->getStyle($columnID.$rowID)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $spreadsheet->getActiveSheet()->getStyle($columnID.$rowID)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                    }
                }
            }

            // $value = $spreadsheet->getActiveSheet()->getCell($columnID."2")->getValue();
            // if(strpos($value,"€")){
            //     for ($row = 2; $row <= $iMaxFil; $row++) {
            //         $value2 = $spreadsheet->getActiveSheet()->getCell($columnID.$row)->getValue();
            //         $spreadsheet->getActiveSheet()->setCellValue($columnID.$row,trim(str_replace("€","",$value2)));
            //     }

            //     $spreadsheet->getActiveSheet()->getStyle($columnID."2:".$columnID.$iMaxFil)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            //     $spreadsheet->getActiveSheet()->getStyle($columnID."2:".$columnID.$iMaxFil)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);


            // }
            if(Datetime::createFromFormat("d-m-Y",$value)){
                for ($row = 2; $row <= $iMaxFil; $row++) {
                    $value2 = $spreadsheet->getActiveSheet()->getCell($columnID.$row)->getValue();
                    // if($value2 <= 0){
                    //     $spreadsheet->getActiveSheet()->getStyle("'".$columnID.$row."'")->applyFromArray(
                    //          array(
                    //             'font'  => array(
                    //                 'color' => array('rgb' => 'FF0000'),
                    //             ))
                    //     );
                    // }
                    $value2 = DateTime::createFromFormat("d-m-Y",$value2);
                    $spreadsheet->getActiveSheet()->setCellValue($columnID.$row,\PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value2));
                }

                $spreadsheet->getActiveSheet()->getStyle($columnID."2:".$columnID.$iMaxFil)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle($columnID."2:".$columnID.$iMaxFil)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

            }
        }

        // recorremos la desde la segunda fila hasta la máxima y vamos estableciento la altura de cada fila.
        // foreach(range('2',$iMaxFil) as $rowID) {
        //     $spreadsheet->getActiveSheet()->getRowDimension($rowID)->setRowHeight(20);

        // }
        $spreadsheet->getActiveSheet()->getStyle("A1:".$iMaxCol.$iMaxFil)
            ->getAlignment()->setWrapText(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$this->filename.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

    }

    function getSheetsNames(){
        $objPHPExcel = IOFactory::load($this->filename);
        return $objPHPExcel->getSheetNames();
    }
    function import(){
        $objPHPExcel = IOFactory::load($this->filename);
        if (!empty($this->sheetname)) {
            $index = array_search($this->sheetname, $objPHPExcel->getSheetNames());
        } else {
            $index = 0;
        }
        $aData = array();
        if ($index !== false) {
            $highestColumm = $objPHPExcel->setActiveSheetIndex($index)->getHighestColumn();
            $highestRow = $objPHPExcel->setActiveSheetIndex($index)->getHighestRow();

            $highestColumm = (strlen($highestColumm)>1 || $highestColumm > 'Z') ? 'Z':$highestColumm;
            $highestColumm++;
            for ($row = 1; $row < $highestRow + 1; $row++) {
                for ($column = 'A'; $column != $highestColumm; $column++) {
                    $data = $objPHPExcel->setActiveSheetIndex($index)->getCell($column . $row);
                    if ($data->isFormula()) {
                        $value = $data->getCalculatedValue();
                    } elseif(Date::isDateTime($data)) {
                        $InvDate = $data->getValue();
                        //$date = date_timestamp_get(Date::excelToDateTimeObject($InvDate));
                        //$value = date('d/m/Y', $date);
                        $value = Date::excelToDateTimeObject($InvDate);
                    } else {
                        $value = $data->getValue();
                        $date = $value;
                        $date = str_replace("'", "", $date);
                        $date = str_replace("-", "/", $date);
                        $date = date_create_from_format('d/m/Y', $value);
                        if ($date !== false) {
                            $value = $date;
                        }
                    }
                    $aData[$row][$column] = $value;
                }
            }
        }
        return $aData;
    }
}
