<?php

namespace Blog\Controllers;

class AuthController extends Controller
{
    protected $modelName = \Blog\Models\User::class;
    private $data = [];
    private string $password;
    public $errorMessage = "";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Checks if the user is logged in
     * @return bool
     */
    public function is_login(): bool
    {
        if (SessionController::get('auth','login')<>"") {
            return true;
        }
        return false;
    }

    /**
     * Checks if the user is an administrator
     * @return true or redirect to homepage
     */
    public static function is_admin()
    {
        if (SessionController::get('userType','login')<>"") {
            if (SessionController::get('userType','login') == 'admin') {
                return true;
            } else {
                Controller::redirect('/');
            }
        }
        Controller::redirect('/');
    }

    /**
     * Redirects user if not logged in
     * @return void
     */
    public static function force_login()
    {
        if (empty(SessionController::get('auth','login'))) {
            Controller::redirect('/?login=true');
        }
    }

    /**
     * Function allowing (or not) the user to log in
     * @return json object
     */
    public function login()
    {
        if (empty($this->var->request->all())) {
            $this->redirect('/?login=true');
        }
        $this->data = [];
        $user = "";
        $error = 0;
        $this->password = "";
        $json = [];
        $success = "";
        $message = "";
        $this->errorMessage = "";

        $this->data['email'] = $this->sanitize($this->var->request->get('emailLogin'));
        $this->data['password'] = $this->sanitize($this->var->request->get('passwordLogin'));

        // Check if values not empty
        if (empty($this->data['email']) || empty($this->data['password'])) {
            $this->errorMessage .= $this->li_alert("Merci de renseigner tous les champs obligatoire !");

            $error++;
        }

        // Check if email exist
        if ($this->model->getEmail($this->data['email']) == 1) {

            $user = $this->model->read($this->data['email'], 'email');
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
            $this->model->update($this->data['email'], ["lastConnectionDate" => $this->data['lastConnectionDate']], 'email');
        }

        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
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
        if (empty($this->var->request->all())) {
            $this->redirect('/?register=true');
        }
        $this->data = [];
        $passwordRepeat = "";
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $this->errorMessage = "";

        $this->data['firstName'] = $this->sanitize($this->var->request->get('firstName'));
        $this->data['lastName'] = $this->sanitize($this->var->request->get('lastName'));
        $this->data['email'] = $this->sanitize($this->var->request->get('emailRegister'));
        $this->data['password'] = $this->sanitize($this->var->request->get('passwordRegister'));
        $this->data['regitrationDate'] = date('Y-m-d H:i:s');
        $passwordRepeat = $this->sanitize($this->var->request->get('passwordRepeat'));

        if ($this->model->getEmail($this->data['email']) == 1) {
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
            $this->model->insert($this->data);
            $this->data['subject'] = EnvironmentController::get('TITLE_WEBSITE'). " - Inscription en attente d'approbation";
            $this->data['message'] = "Votre inscription a bien été pris en compte. L'administrateur validera votre inscription très rapidemment. A bientôt !";
            $message = $this->div_alert("Inscription réussie. <br/> Patience... Votre compte est en attente de validation par l'administrateur.", "success");
            $this->sendMessage($this->data);
            $success = true;
        }
        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
    }
    /**
     * Function lostPassword
     * 
     * @return void
     */
    public function lostPassword()
    {
        if (empty($this->var->request->all())) {
            $this->redirect('/');
        }
        $emailLostPassword = "";
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $this->errorMessage = "";

        $emailLostPassword = $this->sanitize($this->var->request->get('emailLostPassword'));

        if ($this->model->getEmail($emailLostPassword) == 0) {
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
            $this->model->update($emailLostPassword, $this->data, 'email');

            $this->data['subject'] = EnvironmentController::get('TITLE_WEBSITE') . " - Réinitialisation mot de passe";
            $this->data['message'] = "Voici un lien pour réinitialiser votre mot de passe : " . $this->var->server->get('HTTP_REFERER'). "renew/" . $this->data['token'];
            $this->data['email'] = $emailLostPassword;
            $this->data['firstName'] = "";
            $this->data['lastName'] = "";
            $this->sendMessage($this->data);
        }
        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
    }

    /**
     * Function lostPassword
     * 
     * @return void
     */
    public function savePassword()
    {
        if (empty($this->var->request->all())) {
            $this->redirect('/');
        }
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $passwordOld = "";
        $passwordRepeat = "";
        $id = "";
        $this->errorMessage = "";

        $this->data['password'] = $this->sanitize($this->var->request->get('passwordRenew'));
        $id = $this->sanitize($this->var->request->get('id'));
        $passwordRepeat = $this->sanitize($this->var->request->get('passwordRepeatRenew'));
        $user = $this->model->read($id);
        if (!empty($this->var->request->get('passwordOldChange'))) {
            $passwordOld = $this->var->request->get('passwordOldChange');
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
            $this->model->update($id, $this->data);

            $this->data['subject'] = EnvironmentController::get('TITLE_WEBSITE') . " - Votre mot de passe a bien été changé";
            $this->data['message'] = "Votre mot de passe a bien été changé.";

            $this->data['email'] = $user['email'];
            $this->data['firstName'] = $user['firstName'];
            $this->data['lastName'] = $user['lastName'];
            $this->sendMessage($this->data);
        }
        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
    }
}
