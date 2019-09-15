<?php
namespace App\Domain;

use App\Model\restaurant as ModelRestaurant;

class restaurant {

    public function getSomeRest($offset,$limit) {
        $model = new ModelRestaurant();
        return $model->getSomeRest($offset,$limit);
    }

    public function getOneRest($id) {
        $model = new ModelRestaurant();
        return $model->getOneRest($id);
    }
}
