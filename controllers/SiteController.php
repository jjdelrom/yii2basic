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
}

        
      