<?php

namespace App\Http\Controllers;
use DB;
use View;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function CsvForm()
    {   
        return view('upload');
    }
    
    public function allUsersForm()
    {
        $users = DB::table('users')->get();

        return view('all-users', ['users' => $users]);
    }
    
    public function password()
    {
        $password = "";

        $string = "abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ023456789";
        $string_length = strlen($string);

        for($i = 1; $i <= 6; $i++)
        {
            $rand = mt_rand(0,($string_length-1));
            $password .= $string[$rand];
        }

        return $password;   
    }
    
     public function usersList(Request $request)
    {   
        $upload=$request->file('upload_file');
        $filePath=$upload->getRealPath();
        $file=fopen($filePath, 'r');

        $array = array();
        $index = 0;

        $f = fopen($filePath, 'r');

        while($lg = fgetcsv($f,1000,';')) 
        {
            if($lg && array($lg))
            {
                $array[$index] = $lg;     
                
                $nom = $array[$index][0];
                $prenom = $array[$index][1];
                $email = $array[$index][2];
                $pwd=self::password();

                $user= new User;
                $user->name=$nom;
                $user->surname=$prenom;
                $user->email=$email;
                $user->password=hash('sha256', $pwd);
                $user->save();
            }
            $index = $index + 1;
        }
        fclose($f);
    }
}
