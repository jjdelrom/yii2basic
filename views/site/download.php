<?php use yii\helpers\Url; ?>

<?php if (Yii::$app->session->hasFlash('errordownload')): ?>
<strong class="label label-danger">¡Ha ocurrido un error al descargar el archivo!</strong>

<?php else: ?>
<a href="<?= Url::toRoute(["site/download", "file" => "TARIFAS-2016-SDUPO-FORMATO-PRESUPUESTOV2_2015_12_02-1.pdf"]) ?>">Descargar archivo</a><br>
<a href="<?= Url::toRoute(["site/download", "file" => "Sin.png"]) ?>">Descargar archivo</a>

<?php endif; ?>
