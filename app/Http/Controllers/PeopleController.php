<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;

class PeopleController extends Controller
{
  public function index()
  {
      return Person::all();
  }

  public function show(Person $person)
  {
      return $person;
  }

  public function store(Request $request)
  {
      $person = Person::create($request->all());

      return response()->json($person, 201);
  }

  public function update(Request $request, Person $person)
  {
      $person->update($request->all());

      return response()->json($person, 200);
  }

  public function delete(Person $person)
  {
      $person->delete();

      return response()->json(null, 204);
  }
}
