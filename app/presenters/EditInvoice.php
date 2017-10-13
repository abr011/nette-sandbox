<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Database\Context;


class EditInvoicePresenter extends BasePresenter
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
    public function actionDefault($invoiceId = 0)
    {
        $this->invoice = $this->database->table('invoice')->wherePrimary($invoiceId)->fetch();
        //abych ty data moh pouzivat dal ve formulari, typicky pro predvyplneni - setdefault
    }

	public function renderDefault()
	{
        $this->template->invoice = $this->invoice;

	}


    protected function createComponentEditInvoiceForm()
    {
        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('number_order', 'Číslo faktury ')->setRequired();
        $form->addText('number_year', '')->setRequired();
        $form->addText('issue_date', 'Datum vystavení ')->setRequired();
        $form->addText('mature_date', 'Datum splatnosti ')->setRequired();

        $form->addText('amount', 'Částka')->setRequired();
        $form->addText('for_what', 'Popis činnosti (nepovinné)')->setRequired();
        $form->addText('thanks', 'Text poděkování (nepovinné)')->setRequired();


        $form->addSubmit('send', 'Uložit');


        $form->setDefaults(["number_order" => $this->invoice->number_order]);
        $form->setDefaults(["number_year" => $this->invoice->number_year]);
        $form->setDefaults(["issue_date" => $this->invoice->issue_date]);
        $form->setDefaults(["mature_date" => $this->invoice->mature_date]);
        $form->setDefaults(["amount" => $this->invoice->amount]);
        $form->setDefaults(["for_what" => $this->invoice->for_what]);
        $form->setDefaults(["thanks" => $this->invoice->thanks]);



        $form->onSuccess[] = [$this, 'editInvoiceSucceeded'];

        return $form;
    }

    public function editInvoiceSucceeded($form, $values)
    {
        $this->database->table('invoice')->wherePrimary($this->invoice->id)->update([
            "number_order" => $values->number_order,
            "number_year" => $values->number_year,
            "issue_date" => $values->issue_date,
            "mature_date" => $values->mature_date,
            "amount" => $values->amount,
            "for_what" => $values->for_what,
            "thanks" => $values->thanks
            ]);
        $this->flashMessage("Uloženo");
        $this->redirect("this");
    }
}

