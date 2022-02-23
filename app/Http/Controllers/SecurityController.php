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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $personSecurityInfo)
    {
        $person = new Person();

        $person->first_name = $personSecurityInfo->first_name;
        $person->last_name = $personSecurityInfo->last_name;
        $person->middle_name = $personSecurityInfo->middle_name;
        $person->email = $personSecurityInfo->email;
        $person->primary_phone = $personSecurityInfo->primary_phone;
        $person->secondary_phone = $personSecurityInfo->secondary_phone;

        $person->security_info = new SecurityInfo();
        $person->security_info->is_active = $personSecurityInfo->is_active;
        $person->security_info->is_primary_user = $personSecurityInfo->is_primary_user;

        $person->save();

        return $response()->json($person, 200);
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
    public function update(Request $request, $personSecurityInfo)
    {
      $person = Person::find($personSecurityInfo->id);
      if($isset($person))
      {
        $person->first_name = $personSecurityInfo->first_name;
        $person->last_name = $personSecurityInfo->last_name;
        $person->middle_name = $personSecurityInfo->middle_name;
        $person->email = $personSecurityInfo->email;
        $person->primary_phone = $personSecurityInfo->primary_phone;
        $person->secondary_phone = $personSecurityInfo->secondary_phone;

        $securityInfo = $person->securityInfo;
        $canUpdate = true;

        if($personSecurityInfo->is_primary_user == false)
        {
          //Make sure that by setting this to false, we are not removing the last primary user from the database.
          if($this->validateIsNotLastPrimaryUser($person->id) == false)
          {
            //Updating this will remove the last primary user. Return an error.
            $canUpdate = false;
          }
          else
          {
            $securityInfo->is_primary_user = false;
          }
        }

        if($personSecurityInfo->is_active == false)
        {
            if($this.validateIsnotLastActiveUser($personSecurityInfo->id) == false)
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
        ->where(["person_id", "<>", $personId],
                ["is_primary_user", "=", true])
        ->count() > 0;
    }

    private function validateIsnotLastActiveUser($personId)
    {
      return DB::table("security_info")
        ->where(["person_id", "<>", $personId],
                ["is_active", "=", true])
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
