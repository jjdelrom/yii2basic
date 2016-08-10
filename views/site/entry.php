<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <!--Personalizar etiquetas para que no aparezcan los nombres de los campos-->
    <h2>Personalizacion de etiquetas con:<br></h2><h4>$form->field($model, 'name')->label('Your Name')</h4>
    <?= $form->field($model, 'name')->label('Your Name') ?>
    <?= $form->field($model, 'email')->label('Your Email') ?>

  
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>