<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function newsletter(Request $request){

        $email = strip_tags($request->email);
        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
   
        if($isValid == FALSE){
            //echo "Email invalide!!";
            return response()->json(['error' => true,  'message' => "Email invalide !"]);
        }else{
            $exist = Newsletter::where('email', $email)->first();

            if(empty($exist)){
                $newsletter = new Newsletter();
                $newsletter->email = $email;
                $is_saved = $newsletter->save();
                if($is_saved){
                    //echo "Abonnement effectué!!"; 
                    return response()->json(['success' => true, 'message' => "Abonnement effectué !"]);
                    
                }else{
                    //echo "Abonnement echoué!!";
                    return response()->json(['error' => true, 'message' => "Abonnement echoué !"]);
                }
            }else{
                //echo "Vous êtes déjà abonné(e)!!";
                return response()->json(['error' => true, 'message' => "Vous êtes déjà abonné(e) !"]);
            }
        }

    }


}
