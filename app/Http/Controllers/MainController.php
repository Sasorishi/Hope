<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class MainController extends Controller
{
    private function readPDF() {
        //$phpWord = new \PhpOffice\PhpWord\PhpWord();
        $source = public_path().'/RKF - Annexe VDEF.pdf';
        
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source, 'PDF');
    }

    private function pdfParser() {
        $parser = new \Smalot\PdfParser\Parser();
        $source = public_path().'/RKF - Annexe VDEF.pdf';
        $pdf = $parser->parseFile($source);
        $text = $pdf->getText();
        $data = $pdf->getPages()[7]->getDataTm();
        //dump($text);
        dump($data);
    }

    private function checklist() {

    }

    private function controlA() {
        $workforce = false;
        $structure = false;
        

        if (condition) {
            # code...
        }

        $control = [
            "évolution de l'effectif" => $workforce,
            "évolution de la structure" => $structure,
            "évolution de l'activité de exercice" => $activity,
            "éléments essentiels d'évolution de la structure financière" => $financeStructure
        ];

        return $control;
    }

    public function index() {
        $this->pdfParser();
        return view('welcome');
    }
}
