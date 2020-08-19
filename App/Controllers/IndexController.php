<?php 
namespace App\Controllers;
use App\Models\Nota;
use App\Controllers\BaseController;

class IndexController extends BaseController{
    public function indexAction(){
        $listNotas = Nota::all();
        $title = "titulo";

        return $this->renderHTML("index.twig",[
            "title"=>$title,
            "notas"=>$listNotas
            ]);
        //include_once "../views/index.php";
    }
}