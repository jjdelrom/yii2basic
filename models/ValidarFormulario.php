<?php

namespace app\models;
use Yii;
use yii\base\Model;


class ValidarFormulario extends model{
    
    public $nombre;
    public $email;
 
    public function rules()
    {

        return [
         //   [['nombre','email'],'required','message'=>'Campo requeridoo'],

            ['nombre', 'required', 'message' => 'Campo requerido'],

            ['nombre', 'match', 'pattern' => "/^.{3,50}$/", 'message' => 'Mínimo 3 y máximo 50 caracteres'],

            ['nombre', 'match', 'pattern' => "/^[0-9a-z]+$/i", 'message' => 'Sólo se aceptan letras y números'],

            ['email', 'required', 'message' => 'Campo requerido'],

            ['email', 'match', 'pattern' => "/^.{5,80}$/", 'message' => 'Mínimo 5 y máximo 80 caracteres'],

            ['email', 'email', 'message' => 'Formato no válido'],

        ];

    }
    
    
    
    public function rulesPruebasMiasNoLoUso(){
                
        return [
            //  LEER http://www.yiiframework.com/doc-2.0/guide-input-validation.html#declaring-rules
            //  LEER http://www.yiiframework.com/doc-2.0/guide-input-validation.html#declaring-rules
           // ['nombre', 'boolean'], nombre deberia ser 0 o 1
            [['nombre','email'],'required','message'=>'Campo requeridoo'],
            ['nombre','match','pattern'=>"/^.{3,50}+$/",'message'=>'Minimo 3 maximo 50 caracteres'],
           // ['nombre','match','pattern'=>"/^{0-9a-z}+$/i",'message'=>'Solo letras y numeros'],
            ['email','match','pattern'=>'/^.{5,80}+$/','message'=>'Minimo 5 maximo 80 caracteres'],
            ['email','email','message'=>'Formato no validoo'],
          //  ['email', 'filter', 'filter' => 'trim'], no tener espacios
         //   ['nombre', 'boolean', 'trueValue' => true, 'falseValue' => false, 'strict' => true], chekea si es 1 o 0
            /*trueValue: the value representing true. Defaults to '1'.
            falseValue: the value representing false. Defaults to '0'.
            strict: whether the type of the input value should match that of trueValue and falseValue. Defaults to false.*/

            // validates if the value of "password" attribute equals to that of "password_repeat"
       //     ['password', 'compare'],

            // validates if age is greater than or equal to 30
       //     ['age', 'compare', 'compareValue' => 30, 'operator' => '>='], 
       //     'nombre' => [['nombre'], 'string', 'max' => 60],  maximo 60 caracteres
            
/*Para validar los atributos sólo cuando se aplican ciertas condiciones , 
 * por ejemplo, la validación de un atributo depende del valor de otro atributo 
 * se puede utilizar la propiedad cuándo definir tales condiciones . Por ejemplo,

 *   ['state', 'required', 'when' => function($model) {
        return $model->country == 'USA';
    }] */   
            
            /*    // set "username" and "email" as null if they are empty
    [['username', 'email'], 'default'],

    // set "level" to be 1 if it is empty
    ['level', 'default', 'value' => 1],*/
            ];        
    }
    
    public function attributeLabels(){
        
        return[
            'nombre' => 'Nombre',
            'email' => 'Email',
            
        ];                       
    } 
    
    
    
    
}


