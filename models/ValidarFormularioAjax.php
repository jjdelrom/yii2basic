<?php
namespace app\models;
use Yii;
use yii\base\Model;

class ValidarFormularioAjax extends model{

    public $nombre;
    public $email;

    public function rules()
    {
        return [

            ['nombre', 'required', 'message' => 'Campo requerido'],

            ['nombre', 'match', 'pattern' => "/^.{3,50}$/", 'message' => 'Mínimo 3 y máximo 50 caracteres'],

            ['nombre', 'match', 'pattern' => "/^[0-9a-z]+$/i", 'message' => 'Sólo se aceptan letras y números'],

            ['email', 'required', 'message' => 'Campo requerido'],

            ['email', 'match', 'pattern' => "/^.{5,80}$/", 'message' => 'Mínimo 5 y máximo 80 caracteres'],

            ['email', 'email', 'message' => 'Formato no válido'],

            ['email', 'email_existe']  //email_existe es el metodo creado debajo

        ];

    }

    public function attributeLabels()

    {

        return [

            'nombre' => 'Nombre:',

            'email' => 'Email:',

        ];

    }

     

    public function email_existe($attribute, $params)

    {

        $email = ["manuel@mail.com", "antonio@mail.com"];

        foreach($email as $val)
        {
            if($this->email == $val)
            {
                $this->addError($attribute, "El email seleccionado existe");
                return true;
            }
            else
            {
                return false;
            }
        }
    }
}
