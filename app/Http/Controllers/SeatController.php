<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seats;

class SeatController extends Controller
{

    /**
     * Returns number of available seats in the room
     * @return int number of available seats
     */
    static function countAvailableSeats()
    {
        //Counts the available seats in the room
        return Seats::where('occupied', '=', '0')
                    ->count();
    }

    /**
     * return array of all seats available
     * @return int number of available seats
     */
    //
    static function getAvailableSeats()
    {
        return Seats::where('occupied', '=', '0')
                    ->get()
                    ->pluck('seat_nr');
    }

    /**
     * Group the available seats and return them in an array
     * @return array    returns array integers
     */
    static function groupAvailableSeats()
    {
        //Get the array of available seats 
        $seats = self::getAvailableSeats();
        //Sort the seats to be sure code will run smoothly
        $seats->sort();
        //Start creating the available groups
        $groups = array();
        for($i = 0; $i < count($seats); $i++)
        {
            if($i > 0 && ($seats[$i - 1] == $seats[$i] - 1))
                array_push($groups[count($groups) - 1], $seats[$i]);
            else // First value or no match, create a new group
                array_push($groups, array($seats[$i])); 
        }
        return $groups;
    }

    /**
     * Get the nearest seat function
     * @param  int $search Searchtearm that is an integer
     * @param  array $arr    Array where to search in
     * @return int         returns one seatnr that is available in the list
     */
    static function getNearestSeat($search, $arr)
    {
        $nearest = null;
        foreach ($arr as $item) {
          if ($nearest === null || abs($search - $nearest) >= abs($item - $search)) {
             $nearest = $item;
          }
        }
        return $nearest;
    }

   /**
    * Collapse a mixed array into an array
    * @param  array $arr mixed array
    * @return array      returns array
    */
    static function collapseNestedArray($arr)
    {
        $result = [];
        foreach($arr as $value){
            if(is_array($value)){
                foreach($value as $item){
                    $result[] = $item;
                }
            } else {
                    $result[] = $value;
            }
        }
        return $result;
    }

    /**
    * Give a list of seatnrs for the number of persons that need to be seated
    *
    * @param int $visitors the number of persons that need to be seated
    * @return mixed array reserved seatnumbers or null
    */
    public function giveSeatNumbers($visitors)
    {
        $seats = array();
        //Check if there are enough available seats
        if ($visitors > self::countAvailableSeats()) {
            $seats = NULL;
        } else {
            //Get the grouped seats
            $groupedseats = self::groupAvailableSeats();
            //Place visitors in the first available group of seats
            if($visitors <= count(max($groupedseats))){
                foreach($groupedseats as $groupedseat){
                    if ($visitors <= count($groupedseat)){
                        //Remove redundant seats
                        $seats = array_splice($groupedseat, 0, $visitors);  
                        break;
                    }
                }
            } else {
                //if number of visitors exceeds the biggest group of available seats, assign them the biggest group of seats
                $seatsneeded = $visitors;
                //Assign the biggest group available
                $biggestgroup = max($groupedseats);
                array_push($seats, $biggestgroup);
                $seatsneeded =  $seatsneeded - count($biggestgroup);
                //remove that group from the array
                arsort($groupedseats);
                array_shift($groupedseats);
                //sort the remaining groups by size and put them in groups
                $sortedgroupedseats = self::collapseNestedArray($groupedseats);
                //Loop through all groups and assign them the nearest available seats
                foreach($sortedgroupedseats as $sortedgroupedseat) {
                    //calculate average seatnr
                    $reservedseats = array_collapse($seats);
                    $averageseatnr = array_sum($reservedseats) / count($reservedseats);
                    //get nearest seat from the group
                    $nearestseat = array();
                    $nearestseat = self::getNearestSeat($averageseatnr, $sortedgroupedseats);
                    array_push($seats, $nearestseat);
                    $seatsneeded--;
                    //Remove seat from array
                    if(($key = array_search($nearestseat, $sortedgroupedseats)) !== false) {
                        unset($sortedgroupedseats[$key]);
                    }
                    if($seatsneeded == 0){
                        break;
                    }
                }
            }
        }
        //Collapse the mixed array into an array
        if($seats != null){
            $seats = self::collapseNestedArray($seats);
        }
        return $seats;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //available seats in a room
        $allSeats = Seats::get();
        return view('seats.index', compact('allSeats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //For the test, clear all seats to create new seats
        Seats::truncate();
        //Gets number of seats
        $seats = $request->roomsize;
        //generate seats in the room
        for($i = 1; $i <= $seats; $i++ ){
            Seats::create([
                'seat_nr'   => $i,
                'occupied'  => rand(0, 1),
                ])->save();
        }
        //Show success and return room 
       return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Reset all reserved seats to reserve new seats
        Seats::where('reserved', 1)
                ->update(['reserved' => 0]);

        //Get reserved seats and update it in database
        $seats = self::giveSeatNumbers($request->visitors);
        if($seats != null){
            foreach($seats as $seat){
               Seats::where('seat_nr', $seat)
               ->update(['reserved' => 1]); 
            }
            return redirect('/');
        }         
    }
}
