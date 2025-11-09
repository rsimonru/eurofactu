<?php

namespace App\Classes;

use App\Events\FileFinished;
use App\Events\FileProgressUpdate;
use App\Models\Download;
use Illuminate\Support\Facades\Storage;
use XLSXWriter;

class ExcelExport
{
    private $headerStyles = [
        'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'wrap_text' => true, 'color' => '#ffffff',
        'fill' => '#5c9bd1', 'halign' => 'center', 'border' => 'left,right,top,bottom', 'border-style' => 'thin', 'border-color' => '#ffffff'
    ];
    private $cellsStyles = ['font' => 'Arial', 'font-size' => 10, 'border' => 'left,right,top,bottom', 'border-style' => 'thin', 'border-color' => '#cccccc'];
    private $recordsInPage = 1000;
    public $filters = [];
    public $defaultWidth = 11.5;
    public $query = null;
    public $fileName = null;
    public $jobLimit = 500;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }
    public function headings(): array
    {
        return [];
    }
    public function map($record): array
    {
        return [];
    }
    public function setQuery()
    {
        return null;
    }
    // public function store($downloads_id = 0)
    // {
    //     $this->setQuery();
    //     if (!empty($this->query)) {
    //         if ($downloads_id) {
    //             $download = Download::find($downloads_id);
    //             $user_id = $download->user_id;
    //         } else {;
    //             $user_id = auth()->user()->id;
    //             $download = null;
    //         }

    //         $this->fileName = ($this->fileName) ? $this->fileName : $this->query->getModel()->getTable() . '.xlsx';
    //         $excel = new XLSXWriter();
    //         $headers = $this->headings();
    //         $excel->writeSheetHeader('Datos', array_column($headers, 'type', 'title'), $this->headerStyles + ['widths' => array_column($headers, 'width')]);

    //         $count = 0;
    //         if ($download) {
    //             $download->started_at = now();
    //             $download->save();
    //         }
    //         $this->query->chunk($this->recordsInPage, function ($records) use (&$excel, &$count, &$download) {
    //             foreach ($records as $record) {
    //                 $excel->writeSheetRow('Datos', $this->map($record), $this->cellsStyles);
    //                 $count++;
    //             }
    //             if ($download) {
    //                 $download->processed = $count;
    //                 $download->save();
    //                 event(new FileProgressUpdate($download->user_id, $download->id, $count));
    //             }
    //         });
    //         if ($download) {
    //             $download->finished_at = now();
    //             $download->save();
    //         }

    //         $path = 'downloads/' . now()->format('Y') . '/' . now()->format('m') . '/' . now()->format('d') . '/';
    //         $filePath = $path . $user_id.'_'.now()->format('His').'.xlsx';
    //         if (!Storage::exists($path)) {
    //             Storage::makeDirectory($path);
    //             Storage::setVisibility($path, 'public');
    //         }
    //         $excel->writeToFile(storage_path('app/' . $filePath));
    //         Storage::setVisibility($filePath, 'public');
    //         if ($download) {
    //             $download->path = $filePath;
    //             $download->size = Storage::size($filePath);
    //             $download->save();
    //             event(new FileFinished($download->user_id, $download->id));
    //         }
    //     } else {
    //         return false;
    //     }
    // }
    public function download($records = null)
    {
        $this->setQuery();
        if (!empty($this->query)) {
            $this->fileName = ($this->fileName) ? $this->fileName : $this->query->getModel()->getTable() . '.xlsx';
            $excel = new XLSXWriter();
            $headers = $this->headings();
            $excel->writeSheetHeader('Datos', array_column($headers, 'type', 'title'), $this->headerStyles + ['widths' => array_column($headers, 'width')]);

            if ($records) {
                foreach ($records as $record) {
                    $excel->writeSheetRow('Datos', $this->map($record), $this->cellsStyles);
                }
            } else {
                $this->query->chunk($this->recordsInPage, function ($records) use ($excel) {
                    foreach ($records as $record) {
                        $excel->writeSheetRow('Datos', $this->map($record), $this->cellsStyles);
                    }
                });
            }

            return response()->streamDownload(
                fn () => print($excel->writeToString()),
                $this->fileName,
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        } else {
            return false;
        }
    }
}
