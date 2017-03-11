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

        $this->validate($request, ['depart' => 'required', 'destination' => 'required']);
        
        $trajets = Trajet::where('TRJ_DEPART', $request->input('depart'))
                        ->where('TRJ_DESTINATION', $request->input('destination'))->get();
        
        $trajetsEtapesDepart = DB::select('select * from trajets where TRJ_DEPART ="'.$request->input('depart').'" and TRJ_ETAPE1 ="'.$request->input('destination').'" or TRJ_ETAPE2 ="'.$request->input('destination').'";');

        $trajetsEtapesDestination = DB::select('select * from trajets where TRJ_DESTINATION ="'.$request->input('destination').'" and TRJ_ETAPE1 ="'.$request->input('depart').'" or TRJ_ETAPE2 ="'.$request->input('depart').'";');


        // $destination=Trajet::join('villes', 'villes.id', '=', 'trajets.vil_id_destination')->where('villes.vil_nom',$request->input('depart'))->get();
        //$resultat=Trajet::get();

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
    
    public function reserverTrajet(Request $request){
        
        $user = User::find(Session::get('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'));
        $user->reserver()->attach([$request->id]);
            
        $trajet = Trajet::find($request->id);
        
        
        $places = $trajet->TRJ_PLACES;
        $trajet->TRJ_PLACE = $places-1;
        $trajet->save();
        
        return View::make('message')
                        ->with('message', "Reservation effectuée !");

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
        $this->validate($request, ['date' => 'date_format:"d/m/Y', 'heureDepart' => 'date_format:"H:i"', 'heureDestination' => 'date_format:"H:i"', 'depart' => 'required', 'destination' => 'required', 'places' => 'required|integer|between:1,7', 'prix' => 'required|integer|between:1,500']);

        // $depart = $this->ajoutVille($request->depart, $request->departement, $request->longitude, $request->latitude);
        // $destination = $this->ajoutVille($request->destination, $request->destination_departement, $request->destination_longitude, $request->destination_latitude);


        $trajet = new Trajet;
        $datetime = new DateTime();
        $dateDepart = $datetime->createFromFormat('d/m/Y', '$request->date');
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

    public function admin() {
        return view('admin.index');
    }

}
