<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()

    {

        $users  = User::where('role_id','!=',3)->get();
        return view('admin.doctor.index',compact('users'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Doctor create form 
        return view('admin.doctor.create');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


       // dd($request->all());
       
        $this->validateStore($request);

        $data = $request->all();
        $image = $request->file('image');
        $name = (new User)->userAvatar($request);

        $date['image'] = $name;
        $data['password'] = bcrypt($request->password);
        User::create($data);

        return redirect()->back()->with('message','Doctor Added Successfully');

        

        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $user = User::find($id);
        return view('admin.doctor.delete',compact('user'));
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //

        $user = User::find($id);
        return view('admin.doctor.edit',compact('user'));

    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $this->validateUpdate($request,$id);
        // Request All Data From Form
        $data = $request->all();
        // Find USER ID for the user in question
        $user = User::find($id);
        // Declaring the Image Variable 
        $imageName = $user->image;
        // Declaring Password Variable 
        $userPassword = $user->password;
        if($request->hasFile('image')){   
            $imageName = (new User)->userAvatar($request);
            // Removes Previous Image
            unlink(public_path('images/'.$user->image));
    
           
        }
        $data['image'] = $imageName;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }else{
            $data['password'] = $userPassword;
        }
        $user->update($data);

        return redirect()->route('doctor.index')->with('message','Doctor Updated Successfully');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        if(auth()->user()->id == $id){
            abort(401);
        }
        $user = User::find($id);
        $userDelete = $user->delete();
        if($userDelete){
            unlink(public_path('images/'.$user->image));
        }

        return redirect()->route('doctor.index')->with('message','Doctor deleted successfully');


    }

    public function validateStore(Request $request){

        
        
        return  $this->validate($request,[
            'name'=>'required',
            'email'=>'required|unique:users',
            'password'=>'required|min:6|max:25',
            'gender'=>'required',
            'education'=>'required',
            'address'=>'required',
            'department'=>'required',
            'phone_number'=>'required|numeric',
            'image'=>'required|mimes:jpeg,jpg,png',
            'role_id'=>'required',
            'description'=>'required'

       ]);
    }

    public function validateUpdate($request ,$id){

        return  $this->validate($request,[
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$id,


            'password'=>'required|min:6|max:25',
            'gender'=>'required',
            'education'=>'required',
            'address'=>'required',
            'department'=>'required',
            'phone_number'=>'required|numeric',
            'image'=>'required|mimes:jpeg,jpg,png',
            'role_id'=>'required',
            'description'=>'required'

       ]);
    }
}
