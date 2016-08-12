<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<!-- http://localhost:8888/index.php?r=site%2Fvalidar-formulario-ajax -->

<h1>Validar Fromulario Ajax</h1>
<h3><?= $msg ?></h3>

<?php $form = ActiveForm::begin([
    "method" => "post",
    "id"=>"formulario", 
    "enableAjaxValidation" => TRUE,
    "enableClientValidation" => FALSE]); ?>
<div class="form-group">
    <?= $form->field($model, "nombre")->input("text")?>    
</div>
<div class="form-group">
    <?= $form->field($model, "email")->input("email")?>    
</div>
<?= Html::submitButton("Enviar", ["class" => "btn btn-primary"]); ?>
<?php $form->end(); ?>



