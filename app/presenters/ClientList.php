<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;

class ClientListPresenter extends BasePresenter
{

    private $database;

	public function renderDefault()
	{
        $this->template->items = $this->database->table('client');
	}

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

}



