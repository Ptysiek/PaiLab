<?php

namespace Nieruchomosci\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Nieruchomosci\Model\Oferta;
use Nieruchomosci\Model\Zapytanie;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Mpdf\Mpdf;

class ZapytanieController extends AbstractActionController
{
    /**
     * @var Oferta
     */
    private $oferta;

    /**
     * @var Zapytanie
     */
    private $zapytanie;
    private PhpRenderer $phpRenderer;
    /**
     * ZapytanieController constructor.
     * 
     * @param Oferta    $oferta
     * @param Zapytanie $zapytanie
     */
    public function __construct(Oferta $oferta, Zapytanie $zapytanie, PhpRenderer $phpRenderer)
    {
        $this->oferta = $oferta;
        $this->zapytanie = $zapytanie;
        $this->phpRenderer = $phpRenderer;
    }

    public function wyslijAction()
    {
        $id = $this->params()->fromRoute('id');
        if ($this->getRequest()->isPost() && $id) {
            $daneOferty = $this->oferta->pobierz($id);
            $this->zapytanie->dodaj($id, $this->params()->fromPost('tresc'), $this->params()->fromPost('email'), $this->params()->fromPost('telefon'));
            $wynik = $this->zapytanie->wyslij($daneOferty, $this->params()->fromPost('tresc'), $this->params()->fromPost('email'), $this->params()->fromPost('telefon'));

            if ($wynik) {
                $this->getResponse()->setContent('ok');
            }
        }
        return $this->getResponse();
    }
    
    public function wyslijPdfAction()
    {
        $id = $this->params()->fromRoute('id');
        if ($this->getRequest()->isPost() && $id) {
            $daneOferty = $this->oferta->pobierz($id);
            $vm = new ViewModel(['oferta' => $daneOferty]);
            $vm->setTemplate('nieruchomosci/oferty/drukuj');
            $html = $this->phpRenderer->render($vm);

            $mpdf = new Mpdf(['tempDir' => __DIR__ . '/../../../../data/mpdf']);
            $mpdf->WriteHTML($html);
            $pdf = $mpdf->Output('oferta.pdf', 'S');
            $wynik = $this->zapytanie->wyslijPdf($daneOferty, $pdf, $this->params()->fromPost('email'));
            if ($wynik) {
                $this->getResponse()->setContent('ok');
            }
        }
        return $this->getResponse();
    }
}
