<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
  public function index()
  {
      $configuration = Configuration::find(1);
      if(isset($configuration))
      {
        $configuration->primaryUser;
        $configuration->secondaryUsers;

        return $configuration;
      }
      else
      {
        return response()->json(null, 404);
      }
  }

  public function show(Configuration $config)
  {
    return $this->index();
  }

  public function store(Request $request)
  {
      $config = Configuration::create($request->all());

      return response()->json($config, 201);
  }

  public function update(Request $request, Configuration $config)
  {
      $config->update($request->all());

      return response()->json($config, 200);
  }

  public function delete(Configuration $config)
  {
      $config->delete();

      return response()->json(null, 204);
  }
}
