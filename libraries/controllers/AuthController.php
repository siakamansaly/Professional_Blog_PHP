<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


class AuthController extends Controller
{

    private $userModel;

    protected $modelName = \Models\User::class;
    private $data = [];
    private string $password;
    public $post;
    public $errorMessage = "";


    public function __construct()
    {
        $this->userModel = new \Blog\Models\User;
        $this->post = Request::createFromGlobals();
    }


    public function is_login()
    {
        if (isset($_SESSION['login']['auth'])) {
            return true;
        }
    }


    public static function is_admin()
    {
        if (isset($_SESSION['login']['userType'])) {
            if($_SESSION['login']['userType']=='admin')
            {
                return true;
            }
            else
            {
                Controller::redirect('/');
            }
        }
        Controller::redirect('/');
    }


    public static function force_login()
    {
        if (empty($_SESSION['login']['auth'])) {
            Controller::redirect('/?login=true');
        }
    }


    public function login()
    {
        if (empty($this->post->request->all())) {
            $this->redirect('/?login=true');
        }
        $this->data = [];
        $user = "";
        $error = 0;
        $password = "";
        $json = [];
        $success = "";
        $message = "";
        $this->errorMessage ="";

        $this->data['email'] = $this->sanitize($this->post->request->get('emailLogin'));
        $this->data['password'] = $this->sanitize($this->post->request->get('passwordLogin'));

        // Check if values not empty
        if (empty($this->data['email']) || empty($this->data['password'])) {
            $this->errorMessage .= $this->li_alert("Merci de renseigner tous les champs obligatoire !");
            
            $error++;
        }

        // Check if email exist
        if ($this->userModel->getEmail($this->data['email']) == 1) {

            $user = $this->userModel->read($this->data['email'], 'email');
            // Check status user
            if ($user['status'] == 1) {

                $this->password = $user['password'];
                // Check password
                if ((empty(password_verify($this->data['password'], $this->password)))) {
                    $this->errorMessage .= $this->li_alert("L'identifiant ou le mot de passe est incorrect !");
                    $error++;
                }
            } else {
                $this->errorMessage .= $this->li_alert("Ce compte n'est pas actif. Merci de contacter l'administrateur.");
                $error++;
            }
        } else {
            $error++;
            $this->errorMessage = $this->li_alert("Ce compte n'existe pas.");
        }
        $this->errorMessage = $this->ul_alert($this->errorMessage);
        // Construct and send result JSON
        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $message = $this->div_alert("Connexion réussie.", "success");
            $success = true;
            // Assign session variables
            SessionController::set('firstName', $user['firstName'], 'login');
            SessionController::set('lastName', $user['lastName'], 'login');
            SessionController::set('email', $this->data['email'], 'login');
            SessionController::set('userType', $user['userType'], 'login');
            SessionController::set('auth', "true", 'login');
            SessionController::set('regitrationDate', $user['regitrationDate'], 'login');
            SessionController::set('lastConnectionDate', $user['lastConnectionDate'], 'login');
            SessionController::set('id', $user['id'], 'login');
            // Update lastConnectionDate
            $this->data['lastConnectionDate'] = date('Y-m-d H:i:s');
            $this->userModel->update($this->data['email'], ["lastConnectionDate" => $this->data['lastConnectionDate']], 'email');
        }

        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }

    public function logout()
    {
        SessionController::delete('login');
        $this->redirect('/');
    }

    /**
     * Function register
     * 
     * @return void
     */
    public function register()
    {
        //print_r($_POST);die;
        if (empty($this->post->request->all())) {
            $this->redirect('/?register=true');
        }
        $this->data = [];
        $passwordRepeat = "";
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $this->errorMessage ="";

        $this->data['firstName'] = $this->sanitize($this->post->request->get('firstName'));
        $this->data['lastName'] = $this->sanitize($this->post->request->get('lastName'));
        $this->data['email'] = $this->sanitize($this->post->request->get('emailRegister'));
        $this->data['password'] = $this->sanitize($this->post->request->get('passwordRegister'));
        $this->data['regitrationDate'] = date('Y-m-d H:i:s');
        $passwordRepeat = $this->sanitize($this->post->request->get('passwordRepeat'));

        if ($this->userModel->getEmail($this->data['email']) == 1) {
            $this->errorMessage .= $this->li_alert("L'adresse email renseignée est déja inscrite !");
            $error++;
        }
        if ($this->data['password'] <> $passwordRepeat) {
            $this->errorMessage .= $this->li_alert("Les mots de passe saisies ne sont pas identique !");
            $error++;
        } else {
            $this->data['password'] = password_hash($this->data['password'], PASSWORD_DEFAULT);
        }

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $this->userModel->insert($this->data);
            $this->data['subject'] = $_ENV['TITLE_WEBSITE'] . " - Inscription en attente d'approbation";
            $this->data['message'] = "Votre inscription a bien été pris en compte. L'administrateur validera votre inscription très rapidemment. A bientôt !";
            $message = $this->div_alert("Inscription réussie. <br/> Patience... Votre compte est en attente de validation par l'administrateur.", "success");
            $this->sendMessage($this->data);
            $success = true;
        }
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
    }
    /**
     * Function lostPassword
     * 
     * @return void
     */
    public function lostPassword()
    {
        if (empty($this->post->request->all())) {
            $this->redirect('/');
        }
        $emailLostPassword = "";
        $this->data=[];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $this->errorMessage ="";

        $emailLostPassword = $this->sanitize($this->post->request->get('emailLostPassword'));

        if ($this->userModel->getEmail($emailLostPassword) == 0) {
            $this->errorMessage .= $this->li_alert("L'adresse email renseignée n'est pas inscrite !");
            $error++;
        }
        $this->errorMessage = $this->ul_alert($this->errorMessage);
        

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $message = $this->div_alert("Un lien de réinitialisation de mot de passe a été envoyé sur votre boite mail.", "success");
            $success = true;
            $this->data['token'] = uniqid('', false);
            $this->userModel->update($emailLostPassword,$this->data, 'email');

            $this->data['subject'] = $_ENV['TITLE_WEBSITE'] . " - Réinitialisation mot de passe";
            $this->data['message'] = "Voici un lien pour réinitialiser votre mot de passe : ".$_SERVER['HTTP_REFERER']."renew/".$this->data['token'];
            $this->data['email'] = $emailLostPassword;
            $this->data['firstName']="";
            $this->data['lastName']="";
            $this->sendMessage($this->data);

        }
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
    }
    
    /**
     * Function lostPassword
     * 
     * @return void
     */
    public function savePassword()
    {
        if (empty($this->post->request->all())) {
            $this->redirect('/');
        }
        $this->data=[];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $passwordOld = "";
        $passwordRepeat = "";
        $id="";
        $this->errorMessage ="";

        $this->data['password'] = $this->sanitize($this->post->request->get('passwordRenew'));
        $id = $this->sanitize($this->post->request->get('id'));
        $passwordRepeat = $this->sanitize($this->post->request->get('passwordRepeatRenew'));
        $user = $this->userModel->read($id);
        if(!empty($this->post->request->get('passwordOldChange')))
        {
            $passwordOld = $this->post->request->get('passwordOldChange');
            $this->password = $user['password'];

            // Check password
            if ((empty(password_verify($passwordOld, $this->password)))) {
                $this->errorMessage .= $this->li_alert("L'ancien mot de passe est incorrect !");
                $error++;
            }
        }
        if ($this->data['password'] <> $passwordRepeat) {
            $this->errorMessage .= $this->li_alert("Les mots de passe saisies ne sont pas identique !");    
            $error++;
        }
        $this->errorMessage = $this->ul_alert($this->errorMessage);
        

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $message = $this->div_alert("Votre mot de passe a bien été modifié.", "success");
            $success = true;
            $this->data['password'] = password_hash($this->data['password'], PASSWORD_DEFAULT);
            $this->data['token'] = NULL;
            $this->userModel->update($id,$this->data);

            $this->data['subject'] = $_ENV['TITLE_WEBSITE'] . " - Votre mot de passe a bien été changé";
            $this->data['message'] = "Votre mot de passe a bien été changé.";
            
            $this->data['email'] = $user['email'];
            $this->data['firstName']=$user['firstName'];
            $this->data['lastName']=$user['lastName'];
            $this->sendMessage($this->data);

        }
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
    }



    
}
