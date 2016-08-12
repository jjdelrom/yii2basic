<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<a href="<?= Url::toRoute("site/create") ?>">Crear un nuevo alumno</a>

<h3>Lista de alumnos</h3>
<table class="table table-bordered">
    <tr>
        <th>Id Alumno</th>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>Clase</th>
        <th>Nota Final</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach($model as $row): ?>
    <tr>
        <td><?= $row->id_alumno ?></td>
        <td><?= $row->nombre ?></td>
        <td><?= $row->apellidos ?></td>
        <td><?= $row->clase ?></td>
        <td><?= $row->nota_final ?></td>
        <td><a href="#">Editar</a></td>
        <td><a href="#">Eliminar</a></td>
    </tr>
    <?php endforeach ?>
</table>
