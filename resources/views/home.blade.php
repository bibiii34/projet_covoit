@extends('layouts.app2')

@section('content')    
    <script>
      $(function() {
 
    var addresspickerMap = $( "#addresspicker_map" ).addresspicker({
        elements: {
            map: "#map",
            lat: "#lat",
            lng: "#lng",
            locality: '#locality',
            country: '#country'
        }
    });
    var gmarker = addresspickerMap.addresspicker( "marker");
    gmarker.setVisible(true);
    addresspickerMap.addresspicker( "updatePosition");
    
    var addresspickerMap2 = $( "#addresspicker_map2" ).addresspicker({
        elements: {
            map: "#map2",
            lat: "#lat2",
            lng: "#lng2",
            locality: '#locality2',
            country: '#country2'
        }
    });
    var gmarker = addresspickerMap2.addresspicker( "marker");
    gmarker.setVisible(true);
    addresspickerMap2.addresspicker( "updatePosition");
 
});
  
  </script>

      
    <center>
        <h1>Bienvenue sur saliege covoit</h1> 
                <br/><br/>
                

    	<form method="POST" action="{!! url('home') !!}" accept-charset="UTF-8">
		{!! csrf_field() !!}   
	
           <label>Départ : </label> <input id="addresspicker_map" />  
           <label>Destination : </label> <input id="addresspicker_map2" /><br/> 
           
            <input id="locality"  name="depart" readonly="true" type="hidden"> <br/>

            <input id="locality2" name="destination" readonly="true" type="hidden"> <br/>

            <input type="submit" value="Rechercher">	
            
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            
            
                @if(!empty($resultat))
                    <h2>IL Y A DES TRAJETS</h2>
                    
                    @foreach ($resultat as $trajet)
                        <p>Depart: {{ $trajet->TRJ_DEPART }} </p> <br>
                        <p>Destination: {{ $trajet->TRJ_DESTINATION }} </p> <br>
                        <p>Info: {{ $trajet->TRJ_INFO }} </p> <br>
                    @endforeach
                @endif


            
        </form>
                
              
                
             <div id="map"></div>
             <div id="map2"></div>
    </center>
@endsection
