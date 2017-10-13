<?php
declare(strict_types=1);

namespace App\Presenters;


use Nette\Application\UI\Form;
use Nette\Database\Context;

class EditClientPresenter extends BasePresenter
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

    public function actionDefault($clientId = 0)
    {
        $this->client = $this->database->table('client')->wherePrimary($clientId)->fetch();
        //abych ty data moh pouzivat dal ve formulari, typicky pro predvyplneni - setdefault
    }


    public function renderDefault($clientId = 0)
    {
        $this->template->client = $this->client;
    }

    protected function createComponentEditClientForm()
    {
        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('legal_id', 'IČ ') ->setRequired();
        $form->addText('tax_id', 'DIČ ');
        $form->addText('name', 'Název ')->setRequired();
        $form->addText('address_street', 'Ulice ')->setRequired();
        $form->addText('address_town', 'Obec ')->setRequired();
        $form->addText('address_zip', 'PSČ ')->setRequired();

        $form->addSubmit('send', 'Uložit');

        $form->setDefaults(["legal_id" => $this->client->legal_id]);
        $form->setDefaults(["tax_id" => $this->client->tax_id]);
        $form->setDefaults(["name" => $this->client->name]);
        $form->setDefaults(["address_street" => $this->client->address_street]);
        $form->setDefaults(["address_town" => $this->client->address_town]);
        $form->setDefaults(["address_zip" => $this->client->address_zip]);

        $form->onSuccess[] = [$this, 'editClientSucceeded'];

        return $form;
    }

    public function editClientSucceeded($form, $values)
    {
        $this->database->table('client')->wherePrimary($this->client->id)->update(
            ["legal_id" => $values->legal_id,
            "tax_id" => $values->tax_id,
            "name" => $values->name,
            "address_street" => $values->address_street,
            "address_town" => $values->address_town,
            "address_zip" => $values->address_zip]);
        $this->flashMessage("Uloženo");
        $this->redirect("this");
    }
}
