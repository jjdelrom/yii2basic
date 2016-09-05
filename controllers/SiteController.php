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
use app\models\FormRegister;
use app\models\Users;
use yii\web\Session;
use app\models\FormRecoverPass;
use app\models\FormResetPass;
use app\models\User;
use app\models\FormUpload;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
/*    public function behaviors()
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
    }*/

            public function behaviors()
        {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['logout', 'user', 'admin'],
                    'rules' => [
                        [
                            //El administrador tiene permisos sobre las siguientes acciones
                            'actions' => ['logout', 'admin'],
                            //Esta propiedad establece que tiene permisos
                            'allow' => true,
                            //Usuarios autenticados, el signo ? es para invitados
                            'roles' => ['@'],
                            //Este método nos permite crear un filtro sobre la identidad del usuario
                            //y así establecer si tiene permisos o no
                            'matchCallback' => function ($rule, $action) {
                                //Llamada al método que comprueba si es un administrador
                                return User::isUserAdmin(Yii::$app->user->identity->id);
                            },
                        ],
                        [
                           //Los usuarios simples tienen permisos sobre las siguientes acciones
                           'actions' => ['logout', 'user'],
                           //Esta propiedad establece que tiene permisos
                           'allow' => true,
                           //Usuarios autenticados, el signo ? es para invitados
                           'roles' => ['@'],
                           //Este método nos permite crear un filtro sobre la identidad del usuario
                           //y así establecer si tiene permisos o no
                           'matchCallback' => function ($rule, $action) {
                              //Llamada al método que comprueba si es un usuario simple
                              return User::isUserSimple(Yii::$app->user->identity->id);
                          },
                       ],
                    ],
                ],
         //Controla el modo en que se accede a las acciones, en este ejemplo a la acción logout
         //sólo se puede acceder a través del método post
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post'], //solo se puede acceder por post  para acceder con  get tambien se debe añadir ['post','get'],
                    ],
                ],
            ];
        }
    
        public function actionUser(){
            return $this->render("user");
        }
        public function actionAdmin(){
            return $this->render("admin");
        }

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
    public function actionUpload()
    {

     $model = new FormUpload;
     $msg = null;

     if ($model->load(Yii::$app->request->post()))
     {
      $model->file = UploadedFile::getInstances($model, 'file');
      //$model->file = UploadedFile::getInstance($model, 'file'); esta linea es solo si se sube un solo archivo en vez de 4
      //$file = $model->file;
      //$file = saveAs(......);

      if ($model->file && $model->validate()) {
       foreach ($model->file as $file) {
        $file->saveAs('archivos/' . $file->baseName . '.' . $file->extension);
        $msg = "<p><strong class='label label-info'>Enhorabuena, subida realizada con éxito</strong></p>";
       }
      }
     }
     return $this->render("upload", ["model" => $model, "msg" => $msg]);
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

    private function downloadFile($dir, $file, $extensions=[])
    {
     //Si el directorio existe
     if (is_dir($dir))
     {
      //Ruta absoluta del archivo
      $path = $dir.$file;

      //Si el archivo existe
      if (is_file($path))
      {
       //Obtener información del archivo
       $file_info = pathinfo($path);
       //Obtener la extensión del archivo
       $extension = $file_info["extension"];

       if (is_array($extensions))
       {
        //Si el argumento $extensions es un array
        //Comprobar las extensiones permitidas
        foreach($extensions as $e)
        {
         //Si la extension es correcta
         if ($e === $extension)
         {
          //Procedemos a descargar el archivo
          // Definir headers
          $size = filesize($path);
          header("Content-Type: application/force-download");
          header("Content-Disposition: attachment; filename=$file");
          header("Content-Transfer-Encoding: binary");
          header("Content-Length: " . $size);
          // Descargar archivo
          readfile($path);
          //Correcto
          return true;
         }
        }
       }

      }
     }
     //Ha ocurrido un error al descargar el archivo
     return false;
    }

    public function actionDownload()
    {
     if (Yii::$app->request->get("file"))
     {
      //Si el archivo no se ha podido descargar
      //downloadFile($dir, $file, $extensions=[])
      if (!$this->downloadFile("archivos/", Html::encode($_GET["file"]), ["pdf", "txt", "doc", "png"]))
      {
       //Mensaje flash para mostrar el error
       Yii::$app->session->setFlash("errordownload");
      }
     }

     return $this->render("download");
    }
    
public function actionLogin()
 {
            if (!\Yii::$app->user->isGuest) {

                if (User::isUserAdmin(Yii::$app->user->identity->id))
                {
                 return $this->redirect(["site/admin"]);
                }
                else
                {
                 return $this->redirect(["site/user"]);
                }
            }

            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) {

                if (User::isUserAdmin(Yii::$app->user->identity->id))
                {
                 return $this->redirect(["site/admin"]);
                }
                else
                {
                 return $this->redirect(["site/user"]);
                }
                }
            else {
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
 }

 /*   public function actionLogin()
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
    }*/

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
    
 
    private function randKey($str='', $long=0)
       {
           $key = null;
           $str = str_split($str);
           $start = 0;
           $limit = count($str)-1;
           for($x=0; $x<$long; $x++)
           {
               $key .= $str[rand($start, $limit)];
           }
           return $key;
       }

    public function actionConfirm()
    {
       $table = new Users;
       if (Yii::$app->request->get())
       {

           //Obtenemos el valor de los parámetros get
           $id = Html::encode($_GET["id"]);
           $authKey = $_GET["authKey"];

           if ((int) $id)
           {
               //Realizamos la consulta para obtener el registro
               $model = $table
               ->find()
               ->where("id=:id", [":id" => $id])
               ->andWhere("authKey=:authKey", [":authKey" => $authKey]);

               //Si el registro existe
               if ($model->count() == 1)
               {
                   $activar = Users::findOne($id);
                   $activar->activate = 1; //1 = true  el usuario esta activo  0=false
                   if ($activar->update())
                   {
                       echo "Enhorabuena registro llevado a cabo correctamente, redireccionando ...";
                       echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
                   }
                   else
                   {
                       echo "Ha ocurrido un error al realizar el registro, redireccionando ...";
                       echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
                   }
                }
               else //Si no existe redireccionamos a login
               {
                   return $this->redirect(["site/login"]);
               }
           }
           else //Si id no es un número entero redireccionamos a login
           {
               return $this->redirect(["site/login"]);
           }
       }
    }

    public function actionRegister()
    {
     //Creamos la instancia con el model de validación
     $model = new FormRegister;

     //Mostrará un mensaje en la vista cuando el usuario se haya registrado
     $msg = null;

     //Validación mediante ajax
     if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax)
           {
               Yii::$app->response->format = Response::FORMAT_JSON;
               return ActiveForm::validate($model);
           }

     //Validación cuando el formulario es enviado vía post
     //Esto sucede cuando la validación ajax se ha llevado a cabo correctamente
     //También previene por si el usuario tiene desactivado javascript y la
     //validación mediante ajax no puede ser llevada a cabo
     if ($model->load(Yii::$app->request->post()))
     {
      if($model->validate())
      {
       //Preparamos la consulta para guardar el usuario
       $table = new Users;
       $table->username = $model->username;
       $table->email = $model->email;
       //Encriptamos el password
       $table->password = crypt($model->password, Yii::$app->params["salt"]);
       //Creamos una cookie para autenticar al usuario cuando decida recordar la sesión, esta misma
       //clave será utilizada para activar el usuario
       $table->authKey = $this->randKey("abcdef0123456789", 200);
       //Creamos un token de acceso único para el usuario
       $table->accessToken = $this->randKey("abcdef0123456789", 200);

       //Si el registro es guardado correctamente
       if ($table->insert())
       {
        //Nueva consulta para obtener el id del usuario
        //Para confirmar al usuario se requiere su id y su authKey
        $user = $table->find()->where(["email" => $model->email])->one();
        $id = urlencode($user->id);
        $authKey = urlencode($user->authKey);

        $subject = "Confirmar registro";
        $body = "<h1>Haga click en el siguiente enlace para finalizar tu registro</h1>";
        $body .= "<a href='http://yii.local/index.php?r=site/confirm&id=".$id."&authKey=".$authKey."'>Confirmar</a>";

        //Enviamos el correo
        Yii::$app->mailer->compose()
        ->setTo($user->email)
        ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
        ->setSubject($subject)
        ->setHtmlBody($body)
        ->send();
        //vacio los campos del formulario
        $model->username = null;
        $model->email = null;
        $model->password = null;
        $model->password_repeat = null;

        $msg = "Enhorabuena, ahora sólo falta que confirmes tu registro en tu cuenta de correo";
       }
       else
       {
        $msg = "Ha ocurrido un error al llevar a cabo tu registro";
       }

      }
      else
      {
       $model->getErrors();
      }
     }
     return $this->render("register", ["model" => $model, "msg" => $msg]);
    }
    
        public function actionRecoverpass()
        {
         //Instancia para validar el formulario
         $model = new FormRecoverPass;

         //Mensaje que será mostrado al usuario en la vista
         $msg = null;

         if ($model->load(Yii::$app->request->post()))
         {
          if ($model->validate())
          {
           //Buscar al usuario a través del email
           $table = Users::find()->where("email=:email", [":email" => $model->email]);

           //Si el usuario existe
           if ($table->count() == 1)
           {
            //Crear variables de sesión para limitar el tiempo de restablecido del password
            //hasta que el navegador se cierre
            $session = new Session;
            $session->open();

            //Esta clave aleatoria se cargará en un campo oculto del formulario de reseteado
            $session["recover"] = $this->randKey("abcdef0123456789", 200);
            $recover = $session["recover"];

            //También almacenaremos el id del usuario en una variable de sesión
            //El id del usuario es requerido para generar la consulta a la tabla users y 
            //restablecer el password del usuario
            $table = Users::find()->where("email=:email", [":email" => $model->email])->one();
            $session["id_recover"] = $table->id;

            //Esta variable contiene un número hexadecimal que será enviado en el correo al usuario 
            //para que lo introduzca en un campo del formulario de reseteado
            //Es guardada en el registro correspondiente de la tabla users
            $verification_code = $this->randKey("abcdef0123456789", 8);
            //Columna verification_code
            $table->verification_code = $verification_code;
            //Guardamos los cambios en la tabla users
            $table->save();

            //Creamos el mensaje que será enviado a la cuenta de correo del usuario
            $subject = "Recuperar password";
            $body = "<p>Copie el siguiente código de verificación para restablecer su password ... ";
            $body .= "<strong>".$verification_code."</strong></p>";
            $body .= "<p><a href='http://yii.local/index.php?r=site/resetpass'>Recuperar password</a></p>";

            //Enviamos el correo
            Yii::$app->mailer->compose()
            ->setTo($model->email)
            ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
            ->setSubject($subject)
            ->setHtmlBody($body)
            ->send();

            //Vaciar el campo del formulario
            $model->email = null;

            //Mostrar el mensaje al usuario
            $msg = "Le hemos enviado un mensaje a su cuenta de correo para que pueda resetear su password";
           }
           else //El usuario no existe
           {
            $msg = "Ha ocurrido un error";
           }
          }
          else
          {
           $model->getErrors();
          }
         }
         return $this->render("recoverpass", ["model" => $model, "msg" => $msg]);
        }

        public function actionResetpass()
        {
         //Instancia para validar el formulario
         $model = new FormResetPass;

         //Mensaje que será mostrado al usuario
         $msg = null;

         //Abrimos la sesión
         $session = new Session;
         $session->open();

         //Si no existen las variables de sesión requeridas lo expulsamos a la página de inicio
         if (empty($session["recover"]) || empty($session["id_recover"]))
         {
          return $this->redirect(["site/index"]);
         }
         else
         {

          $recover = $session["recover"];
          //El valor de esta variable de sesión la cargamos en el campo recover del formulario
          $model->recover = $recover;

          //Esta variable contiene el id del usuario que solicitó restablecer el password
          //La utilizaremos para realizar la consulta a la tabla users
          $id_recover = $session["id_recover"];

         }

         //Si el formulario es enviado para resetear el password
         if ($model->load(Yii::$app->request->post()))
         {
          if ($model->validate())
          {
           //Si el valor de la variable de sesión recover es correcta
           if ($recover == $model->recover)
           {
            //Preparamos la consulta para resetear el password, requerimos el email, el id 
            //del usuario que fue guardado en una variable de session y el código de verificación
            //que fue enviado en el correo al usuario y que fue guardado en el registro
            $table = Users::findOne(["email" => $model->email, "id" => $id_recover, "verification_code" => $model->verification_code]);

            //Encriptar el password
            $table->password = crypt($model->password, Yii::$app->params["salt"]);

            //Si la actualización se lleva a cabo correctamente
            if ($table->save())
            {

             //Destruir las variables de sesión
             $session->destroy();

             //Vaciar los campos del formulario
             $model->email = null;
             $model->password = null;
             $model->password_repeat = null;
             $model->recover = null;
             $model->verification_code = null;

             $msg = "Enhorabuena, password reseteado correctamente, redireccionando a la página de login ...";
             $msg .= "<meta http-equiv='refresh' content='5; ".Url::toRoute("site/login")."'>";
            }
            else
            {
             $msg = "Ha ocurrido un error";
            }

           }
           else
           {
            $model->getErrors();
           }
          }
         }

         return $this->render("resetpass", ["model" => $model, "msg" => $msg]);

        }    

        
        
        
}//fin de la clase

        
      