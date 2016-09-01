<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
//JJ
use app\models\EntryForm;
use app\models\ValidarFormulario;
use app\models\ValidarFormularioAjax;
//para trabajar ocn ajax hay que agregar tambien estas dos
use yii\widgets\ActiveForm;
use yii\web\Response;
use app\models\FormAlumnos;
use app\models\Alumnos;
use app\models\FormSearch;
use yii\helpers\Html;
use yii\data\Pagination;
use yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    
    /*JJ*/
    public function actionHello($message = 'Hello') /*Si no se pasa parametro, por defecto muestra Hello*/
    {
        return $this->render('hello', ['message' => $message]);
    }  
    /*JJ*/
    public function actionByeBye($message = 'Bye Bye') /*Si no se pasa parametro, por defecto muestra Bye Bye*/
    {
        return $this->render('bye-bye', ['message' => $message]);
        
    }//Hay que tener cuidado como se llama a la pagina, actionByeBye se le llama bye-bye
//    public function actionHelloWorld()
//    {
//        return 'Hello World';
//    }  
    public function actionSaluda(){
        
        //HAY 2 FORMAS DE PASAR VARIABLES una con  $this->view->params['mensaje']... y otra en el return
                
         //se lo pasamos a la vista
        $this->view->params['mensaje'] = 'Hola Mundo cruel';
        $this->view->params['numeros'] = [1,2,3,4,5];
        $this->view->params['get'] = Yii::$app->request->get();
        
            
        $m = "<br>TEXTO MENSAJE 2<br>";
        
        return $this->render("saluda",["mensaje2"=> $m]);//saluda es la vista
        //para acceder: http://localhost:8888/index.php?r=site/saluda
    }
    public function actionFormulario($mensaje = NULL){
        
        
        return $this->render("formulario",["mensaje" => $mensaje]);
    }
    
    public function actionRequest()
    {        
        $mensaje = NULL;
        if(isset($_REQUEST["nombre"]) && $_REQUEST["nombre"] != "") //o $_GET    Yii::$app->request
        {
            $mensaje = "Nombre introducido correctamente: ".$_REQUEST["nombre"];
        }
        $this->redirect(["site/formulario","mensaje" => $mensaje]);
                                            //parametros get, se le pueden añadir mas parametros
    }
    public function actionValidarFormulario(){
        
        $model = new ValidarFormulario;
        if($model->load(\Yii::$app->request->post())){
            if($model->validate()){
                //Por ejemplo consultar en la base de datos
            }
            else {
                $model->getErrors();
            }
        }
        
        return $this->render("validarFormulario",["model"=>$model]);
    }
    public function actionValidarformularioajax()
    {
        $model = new ValidarFormularioAjax;
        $msg = null;        
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax)
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
         
        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->validate())
            {
                //Por ejemplo hacer una consulta a una base de datos
                $msg = "Enhorabuena formulario enviado correctamente";
                $model->nombre = null;
                $model->email = null;
            }
            else
            {
                $model->getErrors();
            }
        }
        return $this->render("validarformularioajax", ['model' => $model, 'msg' => $msg]);
    }
    public function actionCreate(){
        
        $model = new FormAlumnos;
        $msg = null;
        if($model->load(\Yii::$app->request->post())){
            $table = new Alumnos; //\app\models\Alumnos();
            $table->nombre = $model->nombre;
            $table->apellidos = $model->apellidos;
            $table->clase = $model->clase;
            $table->nota_final = $model->nota_final;
            if($table->insert()){
                $msg = "Enhorabuena, registros guardados correctamente";
                $model->nombre = NULL;
                $model->apellidos = NULL;
                $model->clase = NULL;
                $model->nota_final = NULL;
            }
            else {
                $msg = "Ha ocurrido un error";
            }
        }
        else {
            $model->getErrors();
        }
        return $this->render("create",['model'=>$model,'msg'=>$msg]);
    }
//      public function actionView()  //CAMPO DE BUSQUEDA CON CONSULTAS
//    {
//        $table = new Alumnos;
//        $model = $table->find()->all();
//        
//        $form = new FormSearch;
//        $search = null;
//        if($form->load(Yii::$app->request->get()))
//        {
//            if ($form->validate())
//            {
//                $search = Html::encode($form->q);
//                $query = "SELECT * FROM alumnos WHERE id_alumno LIKE '%$search%' OR ";
//                $query .= "nombre LIKE '%$search%' OR apellidos LIKE '%$search%'";
//                $model = $table->findBySql($query)->all();
//            }
//            else
//            {
//                $form->getErrors();
//            }
//        }
//        return $this->render("view", ["model" => $model, "form" => $form, "search" => $search]);
//    }

    public function actionView()
    {
        $form = new FormSearch;
        $search = null;
        if($form->load(Yii::$app->request->get()))
        {
            if ($form->validate())
            {
                $search = Html::encode($form->q);
                $table = Alumnos::find()
                        ->where(["like", "id_alumno", $search])
                        ->orWhere(["like", "nombre", $search])
                        ->orWhere(["like", "apellidos", $search]);
                $count = clone $table;
                $pages = new Pagination([
                    "pageSize" => 1, //elementos por paginas
                    "totalCount" => $count->count() //elementos de la consulta
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
            }
            else
            {
                $form->getErrors();
            }
        }
        else
        { //en caso contrario se muestran todos los registros
            $table = Alumnos::find();
            $count = clone $table;
            $pages = new Pagination([
                "pageSize" => 1,
                "totalCount" => $count->count(),
            ]);
            $model = $table
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }
        return $this->render("view", ["model" => $model, "form" => $form, "search" => $search, "pages" => $pages]);
    }
 //   Clase Pagination: http://www.yiiframework.com/doc-2.0/yii-data-pagination.html
 //   Clase Query: http://www.yiiframework.com/doc-2.0/yii-db-query.html
    //JJ
    public function actionEntry()
    {//1 se crea un EntryForm, se intenta completar los datos de $_post previsto en yii\web\Request::post()
        //si el usuario ha enviado el formulario se llama a validate() para comprobar que los datos son validos
        $model = new EntryForm();
//Yii::$app es una variable global para acceder a request, response, db, etc.
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // valid data received in $model
            // do something meaningful here about $model ...
            return $this->render('entry-confirm', ['model' => $model]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('entry', ['model' => $model]);
        }
    }
    public function actionDelete()
    {
        if(Yii::$app->request->post())
        {
            $id_alumno = Html::encode($_POST["id_alumno"]);
            if((int) $id_alumno)
            {
                if(Alumnos::deleteAll("id_alumno=:id_alumno", [":id_alumno" => $id_alumno]))
                {
                    echo "Alumno con id $id_alumno eliminado con éxito, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("site/view")."'>";
                }
                else
                {
                    echo "Ha ocurrido un error al eliminar el alumno, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("site/view")."'>"; 
                }
            }
            else
            {
                echo "Ha ocurrido un error al eliminar el alumno, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("site/view")."'>";
            }
        }
        else
        {
            return $this->redirect(["site/view"]);
        }
    }  
    
    
    public function actionUpdate()
    {
        $model = new FormAlumnos;
        $msg = null;
        
        if($model->load(Yii::$app->request->post()))
        {
            if($model->validate())
            {
                $table = Alumnos::findOne($model->id_alumno);
                if($table)
                {
                    $table->nombre = $model->nombre;
                    $table->apellidos = $model->apellidos;
                    $table->clase = $model->clase;
                    $table->nota_final = $model->nota_final;
                    if ($table->update())
                    {
                        $msg = "El Alumno ha sido actualizado correctamente";
                    }
                    else
                    {
                        $msg = "El Alumno no ha podido ser actualizado";
                    }
                }
                else
                {
                    $msg = "El alumno seleccionado no ha sido encontrado";
                }
            }
            else
            {
                $model->getErrors();
            }
        }
        
        
        if (Yii::$app->request->get("id_alumno"))
        {
            $id_alumno = Html::encode($_GET["id_alumno"]);
            if ((int) $id_alumno)
            {
                $table = Alumnos::findOne($id_alumno);
                if($table)
                {
                    $model->id_alumno = $table->id_alumno;
                    $model->nombre = $table->nombre;
                    $model->apellidos = $table->apellidos;
                    $model->clase = $table->clase;
                    $model->nota_final = $table->nota_final;
                }
                else
                {
                    return $this->redirect(["site/view"]);
                }
            }
            else
            {
                return $this->redirect(["site/view"]);
            }
        }
        else
        {
            return $this->redirect(["site/view"]);
        }
        return $this->render("update", ["model" => $model, "msg" => $msg]);
    }    
}

        
      