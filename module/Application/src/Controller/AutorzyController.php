<?php

namespace Application\Controller;
use Exception;
use Application\Form\AutorForm;
use Application\Model\Autor;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AutorzyController extends AbstractActionController
{
    /**
     * @var Autor
     */
    private $autor;

    /**
     * @var AutorForm
     */
    private $autorForm;

    public function __construct(Autor $autor, AutorForm $autorForm)
    {
        $this->autor = $autor;
        $this->autorForm = $autorForm;
    }

    public function listaAction()
    {
        return new ViewModel([
            'autorzy' => $this->autor->pobierzWszystko(),
        ]);
    }

    public function szczegolyAction(){
        $id = (int)$this->params()->fromRoute('id');
        if (empty($id)) {
            $this->redirect()->toRoute('autorzy');
        }
        return new ViewModel([
            'autor' => $this->autor->pobierz($id),
        ]);
    }

    public function dodajAction()
    {
        $this->autorForm->get('zapisz')->setValue('Dodaj');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->autorForm->setData($request->getPost());

            if ($this->autorForm->isValid()) {
                $this->autor->dodaj($request->getPost());

                return $this->redirect()->toRoute('autorzy');
            }
        }

        return new ViewModel(['tytul' => 'Dodawanie autora', 'form' => $this->autorForm]);
    }

    public function edytujAction()
    {
        $id = (int)$this->params()->fromRoute('id');
        if (empty($id)) {
            $this->redirect()->toRoute('autorzy');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->autorForm->setData($request->getPost());

            if ($this->autorForm->isValid()) {
                $this->autor->aktualizuj($id, $request->getPost());

                return $this->redirect()->toRoute('autorzy');
            }
        } else {
            $daneAutora = $this->autor->pobierz($id);
            $this->autorForm->setData($daneAutora);
        }

        $viewModel = new ViewModel(['tytul' => 'Edytuj autora', 'form' => $this->autorForm]);
        $viewModel->setTemplate('application/autorzy/dodaj');

        return $viewModel;
    }

    public function usunAction()
    {
        $this->autorForm->get('zapisz')->setValue('Usuń');

        $id = (int)$this->params()->fromRoute('id');
        if (empty($id)) {
            $this->redirect()->toRoute('autorzy');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $daneAutora = $this->autor->pobierz($id);
            $this->autorForm->setData($daneAutora);

            if ($this->autorForm->isValid()) {
                try{
                    $this->autor->usun($id);
                }catch(Exception $e){
                    $viewModel = new ViewModel(['tytul' => 'Potwierdź żądanie usunięcia następującego autora:',
                                                'form' => $this->autorForm,
                                                'autor' => $this->autor->pobierz($id),
                                                'msg' => 'Nie można usunąć autora, usuń najpierw jego książki']);
                    $viewModel->setTemplate('application/autorzy/usun');
                    return $viewModel;
                }

                return $this->redirect()->toRoute('autorzy');
            }
        } 

        $viewModel = new ViewModel(['tytul' => 'Potwierdź żądanie usunięcia następującego autora:',
                                    'form' => $this->autorForm,
                                    'autor' => $this->autor->pobierz($id),
                                    'msg' => '']);
        $viewModel->setTemplate('application/autorzy/usun');

        return $viewModel;
    }
}
