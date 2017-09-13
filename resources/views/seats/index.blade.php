@extends('master.layout')

@section('content')
                <div class="panel-heading">
					<h1>Bioscoop Challenge</h1>
                </div>
                <div class="panel-body">
				<div class="row">
					<div class="col-md-8">
						<p>Beschikbare zitplaatsen zijn zwart, gereserveerde groen en de bezette rood</p>
						@foreach($allSeats as $allSeat)
							@if($allSeat->occupied == 1)
								<span style="color:red">{{$allSeat->seat_nr}}</span>
							@elseif($allSeat->reserved == 1)
								<span style="color:green">{{$allSeat->seat_nr}}</span>
							@else
								<span>{{$allSeat->seat_nr}}</span>
							@endif
						@endforeach
					</div>
					<div class="col-md-4">
						<form method="POST" action="{{route('store.seat')}}">
							{{ csrf_field() }}
							<fieldset class="form-group">
								<label for="roomsize">Zaalgrootte</label>
								<input type="number" class="form-control" id="roomsize" name="roomsize" placeholder="Aantal stoelen">
							</fieldset>
							<button type="submit" class="btn btn-primary">Stel zaal in</button>
							<p>Bezette zitplaatsen worden automatisch gegenereerd voor de opdracht</p>
						</form>
						<hr>
						<form method="POST" action="{{route('update.visitors')}}">
							{{ csrf_field() }}
							<fieldset class="form-group">
								<label for="visitors">Bezoekers</label>
								<input type="number" class="form-control" id="visitors" name="visitors" placeholder="Aantal bezoekers">
							</fieldset>
							<button type="submit" class="btn btn-primary">Reserveer</button>
						</form>
					</div>
                </div>
@endsection