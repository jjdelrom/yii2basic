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

        
      