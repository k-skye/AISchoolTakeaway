<?php
namespace App\Domain;

use App\Model\feedback as ModelFeedback;

class feedback {

     public function addOneFeedBackByDeliver($deliverID,$content) {
        $model = new ModelFeedback();
        return $model->addOneFeedBackByDeliver($deliverID,$content);
    }

}