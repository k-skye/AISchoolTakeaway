<?php
namespace App\Domain;

use App\Model\comment as ModelComment;

class comment {

    public function getSomeComment($restID,$offset,$limit) {
        $model = new ModelComment();
        $arr = $model->getSomeComment($restID,$offset,$limit);
        $i = 0;
        foreach ($arr as $value){
            $arr[$i]['images'] = json_decode($value['images']);
            $i++;
        }
        return $arr;
    }

}
