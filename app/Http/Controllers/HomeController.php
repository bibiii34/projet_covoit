<?php

namespace App\Http\Controllers;

use DB;
use View;
use App\User;
use App\Ville;
use App\Voiture;
use App\Trajet;
use App\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return redirect('/rechercher_un_trajet');
    }

    public function rechercherUnTrajet() {
        return view('rechercher-un-trajet');
    }

    public function resultatRecherche(Request $request) {

        $this->validate($request, ['depart' => 'required', 'destination' => 'required', 'date' => 'required|date_format:d/m/Y|after:today']);
        
  
        $dat = DateTime::createFromFormat('d/m/Y', $request->date);
        $date = $dat->format('Y-m-d');
        
        
        
        $trajets = Trajet::where('TRJ_DEPART', $request->input('depart'))
                        ->where('TRJ_DESTINATION', $request->input('destination'))
                        ->where('TRJ_PLACES', '>', 0)
                        ->where('TRJ_DATE_DEPART', '=',$date)->get();
        
        $trajetsEtapesDepart = DB::select('select * from trajets where TRJ_PLACES > 0 and TRJ_DATE_DEPART ="'.$date.'" and TRJ_DEPART ="'.$request->input('depart').'" and TRJ_ETAPE1 ="'.$request->input('destination').'" or TRJ_ETAPE2 ="'.$request->input('destination').'";');

        $trajetsEtapesDestination = DB::select('select * from trajets where TRJ_PLACES > 0 and TRJ_DATE_DEPART ="'.$date.'" and TRJ_DESTINATION ="'.$request->input('destination').'" and TRJ_ETAPE1 ="'.$request->input('depart').'" or TRJ_ETAPE2 ="'.$request->input('depart').'";');


        return View::make('resultat-recherche')
                        ->with('trajets', $trajets)
                        ->with('trajetsEtapesDepart', $trajetsEtapesDepart)
                        ->with('trajetsEtapesDestination', $trajetsEtapesDestination);
    }

    public function detailsTrajet($id) {
        
        $trajet = Trajet::where('ID',$id)->first();
        
        
        $depart = strtotime(Carbon::parse($trajet->TRJ_HEURE_DEPART)->format('H:i'));
        $destination = strtotime(Carbon::parse($trajet->TRJ_HEURE_DESTINATION)->format('H:i'));
        $duree = gmdate('H:i',$destination-$depart);

        $voiture = Voiture::where('USR_ID', $trajet->USR_ID)->first();
        $user = User::where('ID', $trajet->USR_ID)->first();
        
        
        return View::make('details-trajet')
                ->with('trajet', $trajet)
                ->with('duree', $duree)
                ->with('voiture', $voiture)
                ->with('user', $user);
    }
    
    public function detailsTrajetReservation($id) {
        
        $trajet = Trajet::where('ID',$id)->first();
        
        $depart = strtotime(Carbon::parse($trajet->TRJ_HEURE_DEPART)->format('H:i'));
        $destination = strtotime(Carbon::parse($trajet->TRJ_HEURE_DESTINATION)->format('H:i'));
        $duree = gmdate('H:i',$destination-$depart);

        $voiture = Voiture::where('USR_ID', $trajet->USR_ID)->first();
        $user = User::where('ID', $trajet->USR_ID)->first();
        
        
        return View::make('details-trajet-reservation')
                ->with('trajet', $trajet)
                ->with('duree', $duree)
                ->with('voiture', $voiture)
                ->with('user', $user);
    }
    
    public function detailsTrajetProposer($id) {
        
        $trajet = Trajet::where('ID',$id)->first();
        
        $depart = strtotime(Carbon::parse($trajet->TRJ_HEURE_DEPART)->format('H:i'));
        $destination = strtotime(Carbon::parse($trajet->TRJ_HEURE_DESTINATION)->format('H:i'));
        $duree = gmdate('H:i',$destination-$depart);

        $voiture = Voiture::where('USR_ID', $trajet->USR_ID)->first();
        $user = User::where('ID', $trajet->USR_ID)->first();
        
        
        return View::make('details-trajet-proposer')
                ->with('trajet', $trajet)
                ->with('duree', $duree)
                ->with('voiture', $voiture)
                ->with('user', $user);
    }
    
    public function reserverTrajet(Request $request){
        
        $trajet = Trajet::find($request->id);
        $places = $trajet->TRJ_PLACES;
        
        if ($places > 0) {
            $places = $trajet->TRJ_PLACES-1;
            $user = User::find(Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'));
            $user->reserver()->attach([$request->id]);
            DB::select("update `trajets` set `TRJ_PLACES` = ".$places." where `id` =".$request->id.";" );
            return View::make('message')
                            ->with('message', "Reservation effectuée !");
        }
        else {
             return View::make('message')
                            ->with('message', "Désolé il n'y a plus de place pour ce trajet !");
        }
        

    }
    
    public function annulerReservation(Request $request){
        $user = User::find(Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'));
        $user->reserver()->detach([$request->id]);
        
        $trajet = Trajet::find($request->id);
        $places = $trajet->TRJ_PLACES+1;
        
        DB::select("update `trajets` set `TRJ_PLACES` = ".$places." where `id` =".$request->id.";" );
        return View::make('message')
                        ->with('message', "Reservation Annulée!");
    }
    
    public function mesReservations() {
        $user = User::find(Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'));
        
        $reservations = $user->reserver()->get();
        
        return view('mes-reservations')
            ->with('reservations', $reservations);
    }
    
    public function mesTrajets() {
        $user = User::find(Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'));
        
        $trajets = $user->proposer()->get();
        
        
        
        return view('mes-trajets')
            ->with('trajets', $trajets);
    }
    
    public function proposerUnTrajet() {
        return view('proposer-un-trajet');
    }

    public function validerProposerUnTrajet(Request $request) {
        $this->validate($request, ['date' => 'required|date_format:"d/m/Y"|after:today', 'heureDepart' => 'required|date_format:"H:i"', 'heureDestination' => 'required|date_format:"H:i"', 'depart' => 'required', 'destination' => 'required', 'places' => 'required|integer|between:1,7', 'prix' => 'required|integer|between:0,500', 'bagage' => 'required']);
        
        /*$datecomplete = new DateTime();
        $datetrajet= $datecomplete->createFromFormat('d/m/Y H:i', $request->date.' '.$request->heureDepart);*/

        $trajet = new Trajet;
        $datetime = new DateTime();
        $dateDepart = $datetime->createFromFormat('d/m/Y', $request->date);
        $trajet->TRJ_DATE_DEPART = Carbon::parse($dateDepart)->format('Y-m-d');
        $trajet->TRJ_HEURE_DEPART = Carbon::parse($request->heureDepart)->format('H:i:00');
        $trajet->TRJ_HEURE_DESTINATION = Carbon::parse($request->heureDestination)->format('H:i:00');
        $trajet->TRJ_DEPART = $request->localityDepart;
        $trajet->TRJ_DESTINATION = $request->localityDestination;
        $trajet->TRJ_INFO = $request->informations;
        $trajet->TRJ_PRIX = $request->prix;
        $trajet->TRJ_FLEXIBLE = $request->flexible;
        $trajet->TRJ_PLACES = $request->places;
        $trajet->TRJ_ETAPE1 = $request->localityEtape1;
        $trajet->TRJ_ETAPE2 = $request->localityEtape2;
        $trajet->TRJ_BAGAGE = $request->bagage;
        $trajet->USR_ID = Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        $trajet->save();

        return "trajet enregistré";
    }
    
    public function annulerTrajet(Request $request) {
        DB::delete("delete from trajets where id=".$request->id.";");
        return View::make('message')
                        ->with('message', "Trajet Annulée!");
    }

    public function admin() {
        return view('admin.index');
    }

}
