<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Spatie\PdfToText\Pdf;

use Exception;

class MainController extends Controller
{
    private function pdfParser() {
        $parser = new \Smalot\PdfParser\Parser();
        
        $config = new \Smalot\PdfParser\Config();
        $config->setFontSpaceLimit(-60);
        $config->setDataTmFontInfoHasToBeIncluded(true);

        $parser = new \Smalot\PdfParser\Parser([], $config);
        $source = public_path().'/RKF - Annexe VDEF.pdf';
        $pdf = $parser->parseFile($source);
        
        $data = $pdf->getPages()[5]->getDataTm();
        $text = $pdf->getPages()[1]->getText();

        $content = '';
        for ($i = 0; $i < sizeOf($data); $i++) {
            $element = $data[$i];
            if($i <= sizeOf($data)) {
                $content .= $element[1];
            } else {
                $nextElement = $data[$i + 1];
                if ($element[0][5] + 30 > $nextElement[0][5]) {
                    $content .= $element[1] .'\n\n';
                } else {
                    $content .= $element[1];
                }
            }
            //dump($data[$i]);
        }
        //echo nl2br($text);
        //dump($content);
        //dump($data);
        dump($text);
        return $content;
    }

    private function phpWord() {
        $source = public_path().'/file-sample_100kB.rtf';
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source, 'RTF');
        $text = null;
        dump($phpWord);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                foreach ($element->getElements() as $text) {
                    dump($text->getText());
                }
                //$text .= $this->getWordText($element);
            }
        }
        
        dump($text);
    }

    function getWordText($element) {
        $result = '';
        if ($element instanceof AbstractContainer) {
            foreach ($element->getElements() as $element) {
                $result .= getWordText($element);
            }
        } elseif ($element instanceof Text) {
            $result .= $element->getText();
        }
        // and so on for other element types (see src/PhpWord/Element)
    
        return $result;
    }

    function pdfToText() {
        $source = public_path().'/RKF - Annexe VDEF.pdf';
        $text = (new Pdf('/usr/local/bin/pdftotext'))
        ->setPdf($source)
        ->text();

        dump($text);
    }

    private function checklist($data) {
        $workforce = false;
        $structure = false;
        $activity = false;
        $financeStructure = false;
        $currencyMarket = false;
        $reorganisation = false;

        $modality = false;
        $value = false;
        $amortissement = false;

        $array = $this->control($data, "amortissement");
        //$amortissement = $this->ifValueExists($array);
        
        $controlA = [
            "évolution de l'effectif" => $workforce,
            "évolution de la structure" => $structure,
            "évolution de l'activité de exercice" => $activity,
            "éléments essentiels d'évolution de la structure financière" => $financeStructure,
            "évolution du marché des changes pour l'entreprise" => $currencyMarket,
            "restructuration" => $reorganisation
        ];

        $controlC = [
            "modalité" => $modality,
            "rapprochement entre la valeur comptable" => $value,
            'amortissement utilisé' => $amortissement
        ];
    }

    private function control($text, $keyword) {
        $textDelimited = $this->searchKeyWord($text, $keyword);
        $this->ifValueExists($textDelimited, $keyword);

    }

    private function searchKeyWord($text, $keyword) {
        $pos = strpos($text, $keyword);
        if ($pos === false) {
            echo "The string '$keyword' was not found";
        } else {
            echo "The string '$keyword' was found";
            $start = substr($text, strpos($text, $keyword), -1);
            if (preg_match('/'.$keyword.'(.*?)\./', $start, $match) == 1) {
                $end = $match[0];
                //dump($match);
            }
            //dump($start);
            //$fullString = $keyword.''.$end;
            //dump($fullString);
            return $end;
        }
    }

    private function ifValueExists($text, $searchWord) {
        if($text != NULL) {
            switch ($searchWord) {
                case 'amortissement':
                        dump('amortissement');
                        $words = ['mode linéaire', 'mode dégressif', 'linéaire', 'dégressif'];
                        $this->checkMatchWord($text, $words);
                    break;
                
                case 'taux amortissement':
                        dump('taux amortissement');
                        $words = ['matériel industriel', 'agencements', 'matériel de bureau', 'informatique mobilier'];
                        $this->checkMatchWord($text, $words);
                    break;
            }
        }
    }

    private function checkMatchWord($text, $words) {
        $matchs = null;

        for ($i = 0; $i < sizeOf($words); $i++) { 
            if ($i == sizeOf($words)) {
                $matchs .= $words[$i];
            } else {
                $matchs .= $words[$i]. ' || ';
            }
        }

        if (preg_match('/('.$matchs.')/i', $text)) {
            dump('controle oui');
        } else {
            dump('controle non');
        }
    }

    public function index() {
        $text = $this->pdfParser();
        $this->checkList($text);
        //$this->phpWord();
        //$this->pdfToText();

        return view('welcome');
    }
}
