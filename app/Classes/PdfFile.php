<?php

namespace App\Classes;

use App\Models\TownHall;
use Illuminate\Support\Facades\Storage;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfFile {

    public $templates = [];
    public $records = [];
    public $file_name = 'document';
    public $multi_record = false;
    public $header_footer = true;
    public $page_2x1 = false;

    public function generateFromHTML($sign=false)
	{

        if (length($this->records)>1 && !$this->multi_record) {
            $vcRutaFic = sys_get_temp_dir().'/';
            $zip = new \ZipArchive();
            $zipname = $this->file_name.'.zip';
            $zipurl = $vcRutaFic . $zipname;

            if ($zip->open($zipurl, \ZipArchive::CREATE) !== TRUE) {
                exit("cannot open <$zipurl>\n");
            }
        }

        $pdf_html='';
        $pdf_config = config('pdf');
        $reg_num=0;
        foreach ($this->records as $record_key => $record) {
            foreach ($record as $i => $info) {
                $reg_num++;
                if ($info) {
                    $html = $this->templates[$i];
                    foreach ($info as $key => $value) {
                        $pos=strpos($key,'BIDI_');
                        if ($pos===1) {
                            $bidi='<barcode code="'.$value.'" type="QR" class="barcode" size="2.0" error="M">';
                            $html = str_replace($key, $bidi, $html);
                        } else {
                            $html = str_replace($key, $value, $html);
                        }
                    }
                    $pdf_html = (empty($pdf_html)) ? $html : ((!$this->page_2x1 || ($this->page_2x1 && $reg_num%2==1)) ? $pdf_html."<pagebreak>".$html : $pdf_html.$html);
                }
            }
            if (length($this->records)>1 && !$this->multi_record) {
                $pdf_html = view(($this->header_footer) ? 'pdf.document':'pdf.document-noheaderfooter', ['content' => $pdf_html])->render();
                $pdf = LaravelMpdf::loadHtml($pdf_html,[
                    'margin_left'=> ($sign) ? 15:$pdf_config['margin_left'],
                    'margin_top'=> ($this->header_footer) ? $pdf_config['margin_top']:5,
                    'margin_bottom'=> ($this->header_footer) ? $pdf_config['margin_bottom']:5,
                    'author'=> 'Muniges',
                ]);
                if ($sign) {
                    $tmp_path = tempnam(sys_get_temp_dir(), 'PDF');
                    $pdf->save($tmp_path);
                    $signed_pdf=$this->signPDF($tmp_path);
                    unlink($tmp_path);
                    $pdf_data = $signed_pdf;

                } else {
                    $pdf_data = $pdf->output($this->file_name.'.pdf');
                }
                $pdf_html='';
                $zip->addFromString($this->file_name.'_'.($record_key+1).'.pdf',$pdf_data);
            }
        }
        if (length($this->records)>1 && !$this->multi_record) {
            $this->file_name = $this->file_name.'.zip';
            $zip->close();
            $zip_content = file_get_contents($zipurl);
            unlink($zipurl);
            //dd($this->file_name);
            return $zip_content;
        } else {
            $pdf_html = view(($this->header_footer) ? 'pdf.document':'pdf.document-noheaderfooter',['content' => $pdf_html])->render();
            $pdf = LaravelMpdf::loadHtml($pdf_html,[
                'margin_left'=> ($sign) ? 15:$pdf_config['margin_left'],
                'margin_top'=> ($this->header_footer) ? $pdf_config['margin_top']:5,
                'margin_bottom'=> ($this->header_footer) ? $pdf_config['margin_bottom']:5,
                'author'=> 'Muniges',
            ]);
            $this->file_name = $this->file_name.'.pdf';
            if ($sign) {
                $tmp_path = tempnam(sys_get_temp_dir(), 'PDF');
                $pdf->save($tmp_path);
                $signed_pdf=$this->signPDF($tmp_path);
                unlink($tmp_path);
                return $signed_pdf;
            } else {
                return $pdf->output($this->file_name);
            }
        }
	}

    public function getFilename() {
        return $this->file_name;
    }

    private function signPDF ($vcRutaPDF) {
        if (Storage::exists('aytos/'.session('townhall')->id.'/cert/Sello.p12')) {
            $pdf = new FPDI('P', 'mm', 'A4');
            $pages = $pdf->setSourceFile($vcRutaPDF);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $townhall = TownHall::emtGet(session('townhall')->id);
            $certificate =  Storage::get('aytos/'.session('townhall')->id.'/cert/Sello.p12');
            // $certificatePassword = 'LAPUEBLADEMONTALBAN1';
            $certificatePassword = $townhall->cert_secret;
            $certificateInfo = array();
            $result = openssl_pkcs12_read($certificate, $certificateInfo, $certificatePassword);
            $cert=$certificateInfo['cert'];
            $key=$certificateInfo['pkey'];

            // set additional information
            $info = array(
                'Name' => session('townhall')->name,
                'Location' => 'Ayuntamiento',
                'Reason' => 'Sello de órgano',
                'ContactInfo' => session('townhall')->web,
            );

            for ($i = 1; $i <= $pages; $i++) {
                $pdf->AddPage();
                $page = $pdf->importPage($i);
                $pdf->useTemplate($page, 0, 0, null, null, false);
                if ($i==1) {
                    $x = 3;
                    $y = 265;
                    $pdf->StartTransform();
                    $pdf->Rotate(90, $x, $y);
                    $pdf->Rect($x,$y,50,12);
                    $pdf->image(Storage::path("public/aytos/".session('townhall')->id."/logo_impresos.png"),$x+1,$y+1,0,8);
                    $pdf->SetFontSize(7);
                    $pdf->Multicell(40,0,
                        session('townhall')->name.chr(10).'Sello de Órgano'.chr(10).'Firmado el: '.date("d/m/Y H:i"),
                        0,'L',0,1,$x+10,$y+1);
                    $pdf->StopTransform();
                }
            }
            // set document signature
            $pdf->setSignature($cert, $key, $certificatePassword, '', 2, $info);
            $pdf->setSignatureAppearance($x,$y-50,12,50,1);
            $vcPDF2=$pdf->Output("fichero.pdf","S");
        } else {
            $vcPDF2 = file_get_contents($vcRutaPDF);
        }
        return $vcPDF2;
    }

}
