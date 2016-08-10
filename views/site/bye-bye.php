
<?php //JJ
use yii\helpers\Html;
?>
<?= Html::encode($message) /*Html::encode($message) ES NECESARIO POR 
                                    *QUE PROVIENE DE USUARIO FINAL Y PUEDE HABER ATAQUES XSS*/?>
<!-- Para probarlo:
http://localhost:8888/index.php?r=site%2Fbye-bye&message=Hello%20world-->
