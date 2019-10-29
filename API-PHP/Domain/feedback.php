<?php
namespace App\Domain;

use App\Model\feedback as ModelFeedback;
use App\Model\orders as ModelOders;

class feedback {

    public function addOneFeedBackByDeliver($deliverID,$content) {
        $model = new ModelFeedback();
        return $model->addOneFeedBackByDeliver($deliverID,$content);
    }

    public function addOneFeedBackByUser($userID,$content) {
        $model = new ModelFeedback();
        return $model->addOneFeedBackByUser($userID,$content);
    }

    public function addOneComplaintByUser($userID,$content,$deliverID,$orderID) {
        $model = new ModelFeedback();
        $res = $model->addOneComplaintByUser($userID,$content,$deliverID);
        $modelOrder = new ModelOders();
        $rres = $modelOrder->updateComplaint($orderID);
        if ($rres && $res) {
            return 1;
        }else {
            return -1;
        }
    }
}
