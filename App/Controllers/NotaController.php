<?php 
namespace App\Controllers;
use App\Models\Nota;
use App\Controllers\BaseController;
use Respect\Validation\Validator as v;

class NotaController extends BaseController{

    public function __construct(){
        parent::__construct();
    }

    public function addNota($request){

        $responseMessage = null;

        if($request->getMethod() == 'POST'){
            $dataPost = $request->getParsedBody();
            $notaValidator = v::key('titulo', v::stringType()->notEmpty())
                  ->key('descripcion', v::stringType()->notEmpty());

            try{
                $notaValidator->assert($dataPost);
                /*$nota = new Nota();
                $nota->titulo = $dataPost["titulo"];
                $nota->descripcion = $dataPost["descripcion"];
                $nota->estado = $dataPost["estado"];
                $nota->save();*/

                $files = $request->getUploadedFiles();
                $filesLogo = $files["logo"];
                

                if($filesLogo->getError() == UPLOAD_ERR_OK){
                    $filename = $filesLogo->getClientFilename();
                    $filesLogo->moveTo("uploads/".$filename);
                }

                $responseMessage = "Saved!";

            } catch (\Exception $e){
                $responseMessage = $e->getMessage();
            }   
        }

        return $this->renderHTML("addNotas.twig",[
            "message"=> $responseMessage
        ]);

        //include_once '../views/addNotas.php';
    }
    
}