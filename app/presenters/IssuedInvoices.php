<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;

class IssuedInvoicesPresenter extends BasePresenter
{

    private $database;



    public function renderDefault()
    {
        $this->template->invoices = $this->database->table('invoice')->order('number_order');
        //$this->template->invoice = $invoiceId;
        //$this->template->clients = invoice->related('client');

    }



    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

}

