<?php
namespace App\Domain;

use App\Model\restaurant as ModelRestaurant;

class restaurant {

    public function getRestsByRule($offset,$limit,$condition) {
        $model = new ModelRestaurant();
        switch ($condition) {
            case 'normal':
                return $model->getRestsInNormal($offset,$limit);
                break;
            case 'sale':
                return $model->getRestsInSale($offset,$limit);
                break;
            case 'stars':
                return $model->getRestsInStars($offset,$limit);
                break;
            default:
                return $model->getRestsInNormal($offset,$limit);
                break;
        }
    }

    public function getOneRest($id) {
        $model = new ModelRestaurant();
        return $model->getOneRest($id);
    }
}
