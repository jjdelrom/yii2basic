<?php

namespace app\models;
use Yii;
use yii\base\models;


class ValidarFormulario extends models{
    
    public $nombre;
    public $email;
    
    public function rules(){
        
        
        return [
            ['nombre','required','message'=>'Campo requerido'],
            []
            ];
        
    }
    
    
    
    
    
    
}