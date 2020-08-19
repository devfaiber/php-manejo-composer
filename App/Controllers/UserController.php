<?php 
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\User;
use Respect\Validation\Validator as v;

class UserController extends BaseController{
    public function getAddUser($request){
        $responseMessage = null;


        return $this->renderHTML("addUser.twig",[
            "message"=>$responseMessage
        ]);
    }
    public function postSaveUser($request){
        $responseMessage = null;
        $dataResponse = $request->getParsedBody();

        $notaValidator = v::key('usuario', v::stringType()->notEmpty())
                ->key('email', v::stringType()->notEmpty())
                ->key('password', v::stringType()->notEmpty());


        try{
            $notaValidator->assert($dataResponse);

            $encripted = password_hash($dataResponse["password"], PASSWORD_DEFAULT);

            $user = new User();
            $user->usuario = $dataResponse["usuario"];
            $user->email = $dataResponse["email"];
            $user->password = $encripted;
            $user->save();

            $responseMessage = "Save!";

        } catch(\Exception $e){
            $responseMessage = $e->getMessage();
        }

        return $this->renderHTML("addUser.twig",[
            "message"=>$responseMessage
        ]);
    }
}