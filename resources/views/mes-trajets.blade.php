@extends('layouts.app2')

@section('content')   


@if (count($trajets) > 0)

<h4><strong>Voici les trajets que vous avez proposé</strong></h4>               

    @foreach ($trajets as $trajet)

    <div class="container">


        <div class="row cadre">
            <div class="col-sm-4 center">
                {{ Carbon\Carbon::parse($trajet->TRJ_DATE_DEPART)->format('d-m-Y') }}<br>

                <span class="arial-bold" >1.</span> <span class="arial-bold rouge">Départ : </span> <span class="arial-bold" >{{ $trajet->TRJ_DEPART }}</span>
                <div class="glyphicon glyphicon-arrow-right"></div><br>
                {{ Carbon\Carbon::parse($trajet->TRJ_HEURE_DEPART)->format('H:i') }}

            </div>



            <div class="col-sm-4 center">

                <br>
                <span class="arial-bold" >2.</span><span class="arial-bold rouge">Etape(s) : </span>
                <br>
                @if(!empty($trajet->TRJ_ETAPE1))
                <span class="arial-bold" >{{ $trajet->TRJ_ETAPE1 }} </span>
                <span class="arial-bold" >{{ $trajet->TRJ_ETAPE2 }} </span>
                <span class="arial-bold" >{{ $trajet->TRJ_ETAPE3 }} </span>
                @else <span class="arial-bold" >Pas d'étape</span>
                @endif
            </div>

            <div class="col-sm-4 center">
                <br>
                <div class="glyphicon glyphicon-arrow-right"></div>
                <span class="arial-bold" >3.</span><span class="arial-bold rouge">Destination : </span> <span class="arial-bold" >{{ $trajet->TRJ_DESTINATION }}</span><br>
                {{ Carbon\Carbon::parse($trajet->TRJ_HEURE_DESTINATION)->format('H:i') }}


            </div>
        </div>
        <div class="row padding-right">

            <a href="{{ route('details_trajet_proposer',['id' => $trajet->id]) }}" ><div class="col-sm-offset-10 col-sm-2 details">Voir les details</div></a>
        </div>

    </div>

    @endforeach
    
@else
<h4><strong>Vous n'avez pas proposé de trajet</strong></h4> 
@endif


@endsection


