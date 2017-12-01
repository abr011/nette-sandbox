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
    private $lastClient;

    /**
     * EditClientPresenter constructor.
     */

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function actionDefault($newClientId = 0) //sem mam pridat promeniu, abych predvyplnoval z fa nebo z newclient
    {

        $this->invoice = $this->database->table('invoice')->order('id DESC')
        ->limit(1)->fetch();

        if ($newClientId > 0) {
            $this->lastClient = $this->database->table("client")->wherePrimary($newClientId)->fetch();
            //dump($this->lastClient->toArray());
            //exit;
        } else {

            if ($this->invoice) {

                $this->lastClient = $this->database->table("client")->wherePrimary($this->invoice->client_id)->fetch();

            }
        }



    }

	public function renderDefault()
	{
        $this->template->invoice = $this->invoice;

	}

    protected function createComponentInvoiceForm()
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
        $form->addSubmit('preview', 'Náhled');


        $form->setDefaults([
            "number_order" => $this->invoice->number_order,
            "number_year" => $this->invoice->number_year,
            "issue_date" => $this->invoice->issue_date->format('Y-m-d'),
            "mature_date" => $this->invoice->mature_date->format('Y-m-d'),
            "amount" => $this->invoice->amount,
            "for_what" => $this->invoice->for_what,
            "thanks" => $this->invoice->thanks
        ]);

        if ($this->lastClient)  {
            $form->setDefaults(["client_id" => $this->lastClient->id]);

        }



        $form->onSuccess[] = [$this, 'InvoiceSucceeded'];

        return $form;
    }

    public function InvoiceSucceeded($form, $values)
    {



        if( isset($_POST['preview']) ) {



            $this->redirect("Preview:default");

        }



        else if( isset($_POST['send']) )
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
            $this->redirect("Invoice:default");
        }

    }
}



