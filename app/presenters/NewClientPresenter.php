<?php
declare(strict_types=1);

namespace App\Presenters;


use Nette\Application\UI\Form;
use Nette\Database\Context;

class NewClientPresenter extends BasePresenter
{
    /**
     * @var Context
     */
    private $database;


    private $client; //nacte klient z databaze - souvisi s actionDefault



    /**
     * EditClientPresenter constructor.
     */
    public function __construct(Context $database)
    {
        $this->database = $database;
    }
    /*
        public function actionDefault($clientId = 0)
        {
            $this->client = $this->database->table('client')->wherePrimary($clientId)->fetch();
            //abych ty data moh pouzivat dal ve formulari, typicky pro predvyplneni - setdefault
        }


        public function renderDefault($clientId = 0)
        {
            $this->template->client = $this->client;
        }
         */
    protected function createComponentNewClientForm()
    {
        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('legal_id', 'IČ ') ->setRequired();
        $form->addText('tax_id', 'DIČ ');
        $form->addText('name', 'Název ')->setRequired();
        $form->addText('address_street', 'Ulice ')->setRequired();
        $form->addText('address_town', 'Obec ')->setRequired();
        $form->addText('address_zip', 'PSČ ')->setRequired();

        $form->addSubmit('send', 'Uložit');



        $form->onSuccess[] = [$this, 'newClientSucceeded'];

        return $form;
    }


    public function newClientSucceeded($form, $values)
    {
        $newClient = $this->database->table('client')->insert(
            ["legal_id" => $values->legal_id,
            "tax_id" => $values->tax_id,
            "name" => $values->name,
            "address_street" => $values->address_street,
            "address_town" => $values->address_town,
            "address_zip" => $values->address_zip]);
        $this->flashMessage("Uloženo");

        $this->redirect("Invoice:default", array("newClientId" => $newClient->id)); // neumel jsem to napsat jednoduse
    }
}
