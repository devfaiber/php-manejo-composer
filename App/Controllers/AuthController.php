<?php 
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\User;


class AuthController extends BaseController{
    public function doLogin($request){
        
        return $this->renderHTML("loginForm.twig",[]);
    }
    public function postLogin($request){
        $dataPost = $request->getParsedBody();
        $responseMessage = null;

        $user = User::where("email", $dataPost["usuario"])->first();

        if($user){
            if(password_verify($dataPost["password"], $user->password)){
                $responseMessage = "login is successful";

                $_SESSION["userId"] = $user->id;

                return $this->redirect("/admin");
            } else {
                $responseMessage = "Contraseña y email, no son correctos";
            }
        } else {
            $responseMessage = "Contraseña y email, no son correctos";
        }

        return $this->renderHTML("loginForm.twig", [
            "message"=>$responseMessage
        ]);

    }

    public function getLogout($request){
        unset($_SESSION["userId"]);
        return $this->redirect("/login");
    }

}