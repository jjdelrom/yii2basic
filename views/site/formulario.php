<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\UrlManager;
?>

<h1>Formulario</h1>   
<h3><?= $mensaje; ?></h3>
                                    <!-- //action  //method   //option -->
<?=  HTML::beginForm(  Url::toRoute("site/request"), "get", ['class' => 'form-block'] ); //inline ?>

<div class="form-group ">
                        <!--titulo y for de la etiqueta-->
    <?=    Html::label("Introduce tu nombre", "nombre",["class" => "form-control"]); ?>
    <?=    Html::textInput("nombre", null); ?>
                        <!--nombre del campo de texto, value y clase -->
</div>
<?= Html::submitInput("Enviar nombre",["class" => "btn btn-primary"])  ?>
                                    
    <br>boton simple, no submit
<button type="button" class="btn btn-primary">Primary <span class="badge">7</span></button>
                <!--sumit-->
<?=  Html::endForm() ?>