<?php  //$mensaje; 
echo $this->params['mensaje'].'<br>'; 
echo $this->params['numeros'][0].'<br>';
$array = $this->params['numeros'];

foreach ($array as $key => $value) {
    echo '<strong>Clave</strong>: '.$key." - <strong>Valor</strong>:".$value."<br>";
}

//otra forma de hacer foreach
foreach ($array as $key => $value):
    echo '<strong>Clave:</strong> '.$key." - <strong>Valor</strong>:".$value."<br>";
endforeach;

//imprimir el valor pasado desde el controlador get
print_r($this->params['get']);

echo $mensaje2;
?>  <!-- ?= es equivalente a echo  -->  

