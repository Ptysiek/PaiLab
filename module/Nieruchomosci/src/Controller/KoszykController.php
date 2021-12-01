<?php

namespace Nieruchomosci\Controller;

use Nieruchomosci\Model\Koszyk;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Db\Adapter as DbAdapter;
use Laminas\Db\Sql\Sql;
use Laminas\View\Model\ViewModel;
use Laminas\Session\SessionManager;

class KoszykController extends AbstractActionController
{
    private Koszyk $koszyk;

    /**
     * KoszykController constructor.
     * @param Koszyk $koszyk
     */
    public function __construct(Koszyk $koszyk)
    {
        $this->koszyk = $koszyk;
    }

    public function dodajAction()
    {
        $session = new SessionManager();
		if (! $this->koszyk->isDuplicated( $this->params()->fromRoute('id'), $session->getId() )) {
            $this->koszyk->dodaj($this->params()->fromRoute('id'));
            $this->getResponse()->setContent('ok');
        } else {
            $this->getResponse()->setContent('nope');
        }
        return $this->getResponse();
    }
    public function usunAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->koszyk->usun($this->params()->fromRoute('id'));
            $this->getResponse()->setContent('ok');
        }
        return $this->getResponse();
    }
    public function listaAction(){
        return new ViewModel([
            'koszyk' => $this->koszyk->selectAll(),
        ]);
    }
}
