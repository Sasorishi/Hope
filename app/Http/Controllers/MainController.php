<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Exception;

class MainController extends Controller
{
    private function readWord() {
        $source = public_path().'/file-sample_100kB.docx';
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
        $content = '';

        foreach($phpWord->getSections() as $section) {
            foreach($section->getElements() as $element) {
                if (method_exists($element, 'getElements')) {
                    foreach($element->getElements() as $childElement) {
                        if (method_exists($childElement, 'getText')) {
                            $content .= $childElement->getText() . ' ';
                        }
                        else if (method_exists($childElement, 'getContent')) {
                            $content .= $childElement->getContent() . ' ';
                        }
                    }
                }
                else if (method_exists($element, 'getText')) {
                    $content .= $element->getText() . ' ';
                }
            }
        }
        
        print($content);
        //dump($phpWord);
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
        $activity = false;
        $financeStructure = false;
        $currencyMarket = false;
        $reorganisation = false;

        if(condition) {
            # code...
        }

        $control = [
            "évolution de l'effectif" => $workforce,
            "évolution de la structure" => $structure,
            "évolution de l'activité de exercice" => $activity,
            "éléments essentiels d'évolution de la structure financière" => $financeStructure,
            "évolution du marché des changes pour l'entreprise" => $currencyMarket,
            "restructuration" => $reorganisation
        ];

        return $control;
    }

    private function valueExists($value) {
        switch($value) {
            case 'value':
                    if($value == true) {

                    }
                break;
            
            default:
                # code...
                break;
        }
    }

    public function index() {
        //$this->pdfParser();
        $this->readWord();
        return view('welcome');
    }
}
