<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Database\Context;

class InvoicePresenter extends BasePresenter
{

    /**
     * @var Context
     */
    private $database;

    private $invoice; //nacte klient z databaze - souvisi s actionDefault

    /**
     * EditClientPresenter constructor.
     */

    public function __construct(Context $database)
    {
        $this->database = $database;
    }
    public function actionDefault()
    {

        $this->invoice = $this->database->table('invoice')->order('id DESC')
        ->limit(1)->fetch();

    }

	public function renderDefault()
	{
        $this->template->invoice = $this->invoice;

	}

    protected function createComponentInvoiceForm($newClientId = 0)
    {

        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('number_order', 'Číslo faktury ')->setRequired();
        $form->addText('number_year', '')->setRequired();
        $form->addText('issue_date', 'Datum vystavení ')->setRequired();
        $form->addText('mature_date', 'Datum splatnosti ')->setRequired();

        $clients = $this->database->table('client')->fetchPairs('id', 'name');
        $form->addSelect('client_id', 'Klient', $clients);

        $form->addText('amount', 'Částka')->setRequired();
        $form->addText('for_what', 'Popis činnosti (nepovinné)')->setRequired();
        $form->addText('thanks', 'Text poděkování (nepovinné)')->setRequired();




        $form->addSubmit('send', 'Uložit');


        $form->setDefaults(["number_order" => $this->invoice->number_order]); //pak nekdy zvetsit o jedna
        $form->setDefaults(["number_year" => $this->invoice->number_year]);
        $form->setDefaults(["issue_date" => $this->invoice->issue_date]);  //->format('j. n. Y')
        $form->setDefaults(["mature_date" => $this->invoice->mature_date]);



        echo $newClientId;

        $form->setDefaults(["client_id" => $this->invoice->client_id]);



        $form->setDefaults(["amount" => $this->invoice->amount]);
        $form->setDefaults(["for_what" => $this->invoice->for_what]);
        $form->setDefaults(["thanks" => $this->invoice->thanks]);



        $form->onSuccess[] = [$this, 'InvoiceSucceeded'];

        return $form;
    }

    public function InvoiceSucceeded($form, $values)
    {
        $this->database->table('invoice')->insert([
            "number_order" => $values->number_order,
            "number_year" => $values->number_year,
            "issue_date" => $values->issue_date,
            "mature_date" => $values->mature_date,
            "client_id" => $values->client_id,

            "amount" => $values->amount,
            "for_what" => $values->for_what,
            "thanks" => $values->thanks
        ]);
        $this->flashMessage("Uloženo");
        $this->redirect("this");
    }
}



