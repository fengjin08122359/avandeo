<?php

class search_mdl_associate extends dbeav_model{

    #function save($data,$mustUpdate=null){
    #    if($data['type_id']){
    #        $old = $this->getList('id,words',array('type_id'=>$data['type_id'],'type_from'=>$data['from_type']));
    #        if($old){
    #            if($old[0]['words'] == $data['words']){
    #                return true;
    #            }else{
    #            }
    #        }
    #    }else{
    #        return false;
    #    }
    #    if(strlen($data['words']) > 1){
    #         parent::save($data,$mustUpdate);
    #    }else{
    #        $this->delete(array('type_id'=>$data['type_id'],'type_from'=>$data['from_type']));
    #    }
    #}

}
