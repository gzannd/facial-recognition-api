<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\SecurityInfo;

class SecurityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($personSecurityInfo)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $person = new Person();

        $person->first_name = $request->input("first_name");
        $person->last_name = $request->input("last_name");
        $person->middle_name = $request->input("middle_name");
        $person->email = $request->input("email");
        $person->primary_phone = $request->input("primary_phone");
        $person->secondary_phone = $request->input("secondary_phone");
        $person->security_info_id = null;
        $person->save();

        $security_info = new SecurityInfo();
        $security_info->person_id = $person->id;
        $security_info->is_active = $request->input("is_active");
        $security_info->is_primary = $request->input("is_primary");

        $security_info->save();

        $person->security_info_id = $security_info->id;
        $person->update();
        $person->security_info = $security_info;

        return response()->json($person, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $personInfo = Person::find($id);
        if(isset($personInfo))
        {
          $personInfo->securityInfo;
          return $personInfo;
        }

        return response()->json(null, 404);
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
      $person = Person::find($id);
      if(isset($person))
      {
        $person->first_name = $request->input("first_name");
        $person->last_name = $request->input("last_name");
        $person->middle_name = $request->input("middle_name");
        $person->email = $request->input("email");
        $person->primary_phone = $request->input("primary_phone");
        $person->secondary_phone = $request->input("secondary_phone");

        $securityInfo = SecurityInfo::find($person->security_info_id);
        $canUpdate = true;

        if($request->input("is_primary") == false)
        {
          //Make sure that by setting this to false, we are not removing the last primary user from the database.
          if($this->validateIsNotLastPrimaryUser($id) == false)
          {
            //Updating this will remove the last primary user. Return an error.
            $canUpdate = false;
          }
          else
          {
            $securityInfo->is_primary = false;
          }
        }

        if($request->input("is_active") == false)
        {
            if($this->validateIsNotLastActiveUser($id) == false)
            {
              $canUpdate = false;
            }
            else
            {
              $securityInfo->is_active = false;
            }
        }

        if($canUpdate == true)
        {
          //Update the person and security info.
          $person->update();
          $securityInfo->update();

          return response()->json($person, 200);
        }
        else
        {
          //Return a bad request.
          return response()->json(null, 400);
        }
      }
      else
      {
        //Person wasn't found, return an error.
        return response()->json(null, 404);
      }
    }

    private function validateIsNotLastPrimaryUser($personId)
    {
      return DB::table("security_info")
        ->where("person_id", "<>", $personId)
        ->where("is_primary", "=", true)
        ->count() > 0;
    }

    private function validateIsNotLastActiveUser($personId)
    {
      return DB::table("security_info")
        ->where("person_id", "<>", $personId)
        ->where("is_active", "=", true)
        ->count() > 0;
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
    }
}
