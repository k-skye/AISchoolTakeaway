<?php
namespace App\Domain;

use App\Model\help as ModelHelp;

class help {

     public function getDeliverAllHelp() {
        $model = new ModelHelp();
        return $model->getDeliverAllHelp();
    }

}
