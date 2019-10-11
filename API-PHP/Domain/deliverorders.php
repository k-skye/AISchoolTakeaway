<?php
namespace App\Domain;

use App\Model\deliverorders as ModelDeliverorders;
use App\Model\orders as ModelOders;

class deliverorders {

    public function addOneOrder($orderID,$deliverID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为2-接单状态
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,2);
        $rres = $model->addOneOrder($orderID,$deliverID,$createTime);
        if ($rres && $res) {
            return $rres;
        }else {
            return -1;
        }
    }

}
