<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Time;
use App\Models\User;
use App\Models\Booking;
Use App\Models\Prescription;
use App\Mail\AppointmentMail;


class FrontendController extends Controller
{

    public function index() { 


        if(request('date')){
        $doctors= $this->findDoctorsBasedOnDate(request('date'));

            return view('welcome',compact('doctors'));
        }
        // View Doctors Based On Dates 
        $doctors = Appointment::where('date',date('Y-m-d'))->get();

    
    return view('welcome',compact('doctors')); 
    //
    }

    public function show($doctorId,$date){


        $appointment = Appointment::where('user_id',$doctorId)->where('date',$date)->where('date',$date)->first();

        // Returns Times Which Haven't been taken up
        $times = Time::where('appointment_id', $appointment->id)->where('status',0)->get();

        
        $user = User::where('id',$doctorId)->first();
        $doctor_id = $doctorId;



       // return $times;
        return view('appointment',compact('times','user','doctor_id','date'));
    }

    public function findDoctorsBasedOnDate($date){

        $doctors = Appointment::where('date',$date)->get();
        return $doctors;

    }

    public function store(Request $request){

        $request->validate(['time'=>'required']);
        $check = $this->checkBookingTimeInterval();

        if($check){
            return redirect()->back()->with('errmessage','You already made an appointment. Please wait to make next 
            appointment');
        }

        // Creates Booking in the booking table for the user appoint, Fields are shown below
        Booking::create([ 
            'user_id'=>auth()->user()->id,
            'doctor_id'=>$request->doctorId,
            'time'=>$request->time,
            'date'=>$request->date,
            'status'=>0 
            ]);

            // Updates the Time Status to 1 in the Times Table to show appointment has been booked 
        Time::where('appointment_id', $request->appointmentId)
            ->where('time',$request->time)
            ->update(['status'=>1]);

        // Function To Send Mail
        $doctorName = User::where('id',$request->doctorId)->first();
        $mailData = [
            'name'=>auth()->user()->name,
            'time'=>$request->time,
            'date'=>$request->date,
            'doctorName' => $doctorName->name


        ];
            try{
                \Mail::to(auth()->user()->email)->send(new AppointmentMail($mailData));

        }catch(\Exception $e){

        }
            return redirect()->back()->with('message','Your appointment was booked');

    }




    public function checkBookingTimeInterval(){

        return Booking::orderby('id','desc')
           ->where('user_id', auth()->user()->id)
           ->whereDate('created_at',date('Y-m-d'))
           ->exists();
    }

    public function myBookings(){

        $appointments = Booking::where('user_id', auth()->user()->id)->get();

        return view('booking.index', compact('appointments'));
    }

    public function myPrescription(){

        $prescriptions = prescription::where('user_id', auth()->user()->id)->get();
            return view('my-prescription', compact('prescriptions'));
    }

    public function doctorToday(Request $request)
    {
        $doctors = Appointment::with('doctor')->whereDate('date',date('Y-m-d'))->get();
        return $doctors;
    }

    public function findDoctors(Request $request)
    {
        $doctors = Appointment::with('doctor')->whereDate('date',$request->date)->get();
        return $doctors;
    }
    
}
