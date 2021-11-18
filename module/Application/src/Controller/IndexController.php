<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Data;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        return [
            'naglowek' => 'Testowy nagłówek'
        ];
    }

    public function dataAction()
    {
        $data = new Data();

        return [
            'dzisiaj' => $data->dzisiaj(),
            'dni_tygodnia' => $data->dniTygodnia(),
        ];
    }
}
