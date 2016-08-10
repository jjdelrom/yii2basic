<?php

namespace app\models;

use Yii;
use yii\base\Model;

class EntryForm extends Model
{
    public $name;
    public $email;

    public function rules() //reglas para la validacion de los campos del formulario
    {
        return [
            [['name', 'email'], 'required'],
            ['email', 'email'],
        ]; //ambos son obligatorios, pero email debe tener formato email a@b.com
    }
}


/* VALIDACION PARA PROBAR ERRORES
$model = new EntryForm();
$model->name = 'Qiang';
$model->email = 'bad';
if ($model->validate()) {
    // Good!
} else {
    // Failure!
    // Use $model->getErrors()
}*/