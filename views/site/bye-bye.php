
<?php //JJ
use yii\helpers\Html; 
?>
<?= Html::encode($message) /*Html::encode($message) ES NECESARIO POR 
                                    *QUE PROVIENE DE USUARIO FINAL Y PUEDE HABER ATAQUES XSS*/?>
<!-- Para probarlo:
http://localhost:8888/index.php?r=site%2Fbye-bye&message=Hello%20world-->



<!--

$session = Yii::$app->session;

// Request #1
// set a flash message named as "postDeleted"
$session->setFlash('postDeleted', 'You have successfully deleted your post.');

// Request #2
// display the flash message named "postDeleted"
echo $session->getFlash('postDeleted');

// Request #3
// $result will be false since the flash message was automatically deleted
$result = $session->hasFlash('postDeleted');
-->