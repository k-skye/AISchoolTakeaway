<?php
namespace App\Domain;

use App\Model\homedata as ModelHomeData;

class homedata {

    public function getHeadAdImg() {
        $model = new ModelHomeData();
        return $model->getHeadAdImg();
    }

}
