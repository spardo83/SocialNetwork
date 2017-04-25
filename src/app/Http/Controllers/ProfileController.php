<?php

namespace App\Http\Controllers;

use App\Models\Domicilio;
use App\Models\Imagen;
use Illuminate\Http\Request;
use App\Models\Provincia;
use App\Models\Perfil;
use App\Models\Sexo;
use App\User;
use DB;
use Storage;
use Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = \Auth::user()->id;
        $currentuser = User::find($id);

        $profile = $currentuser->perfil;

        if (empty($profile))
            return redirect()->route('profile.create');


        return redirect()->route('profile.edit');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = Auth::user()->id;
        $currentuser = User::find($id);

        $perfil = $currentuser->perfil;
        if (!empty($perfil))
            return redirect()->route('profile.edit');

        return view('perfil.create')->with('sexos', Sexo::all())->with('provincias', Provincia::all());

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->createUpdate($request);
        return redirect('profile');
    }

    /**
     * Display the specified resource.
     *
     * @param Perfil $perfil
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show(Perfil $perfil)
    {
        //        return view("perfil.edit",compact('perfil'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //


        $id = Auth::user()->id;
        $currentuser = User::find($id);

        $domicilio = Domicilio::with(['perfil' => function ($q) {
            $id = Auth::user()->id;
            $q->where('user_id', $id)->orderBy('pivot_timestamp', 'desc');
        }])->first();

        $imagen = Imagen::with(['perfil' => function ($q) {
            $id = Auth::user()->id;
            $q->where('user_id', $id)->latest();
        }])->first();

        $file = null;
        $extension = null;

        if (!empty($imagen) && Storage::disk('local')->exists($imagen->url)) {
            $file = Storage::get($imagen->url);
            $extension = $imagen->extension;
        }
//dd('data:image/' . pathinfo($file, PATHINFO_EXTENSION) . ';base64,' . empty($file) ? base64_encode($file) : '');

        return view("perfil.edit")
            ->with('perfil', $currentuser->perfil)
            ->with('sexos', Sexo::all())
            ->with('provincias', Provincia::all())
            ->with('domicilio', $domicilio)
            ->with('imagen', !empty($extension) && !empty($file) ?  'data:image/' . $extension. ';base64,' . base64_encode($file) : null )
            ->with('departamento_id', $domicilio->localidad->departamento_id)
            ->with('provincia_id', $domicilio->localidad->departamento->provincia_id);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Perfil $perfil
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function update(Request $request)
    {
        //
        $this->createUpdate($request);

        return back();
    }

    private function createUpdate(Request $request)
    {

        DB::beginTransaction(); //Start transaction!

        try {
            $id = Auth::user()->id;
            $currentuser = User::find($id);

            if (!is_null($currentuser->perfil)) {
                $currentuser->perfil()->update([
                    'nombre' => $request['nombre'],
                    'apellido' => $request['apellido'],
                    'telefono' => $request['telefono'],
                    'fechanacimiento' => $request['fechanacimiento'],
                    'sexo_id' => $request['sexo_id']
                ]);

                $domicilio = Domicilio::with(['perfil' => function ($q) {
                    $id = Auth::user()->id;
                    $q->where('user_id', $id)->orderBy('pivot_timestamp', 'desc');
                }])->first();

                $domicilio->update([
                    'calle' => $request['calle'],
                    'nro' => $request['nro'],
                    'localidad_id' => $request['localidad_id'],
                    'lat' => $request['lat'],
                    'long' => $request['long']
                ]);

            } else {

                $perfil = $currentuser->perfil()->create([
                    'nombre' => $request['nombre'],
                    'apellido' => $request['apellido'],
                    'telefono' => $request['telefono'],
                    'fechanacimiento' => $request['fechanacimiento'],
                    'sexo_id' => $request['sexo_id']
                ]);


                $domicilio = new Domicilio([
                    'calle' => $request['calle'],
                    'nro' => $request['nro'],
                    'localidad_id' => $request['localidad_id'],
                    'lat' => $request['lat'],
                    'long' => $request['long']
                ]);

                $domicilio->save();

                $perfil->domicilio()->attach($domicilio);
            }


            if ($request->hasFile('photo') && $request->photo->isValid()) {


                $path = $request->photo->store('profiles');

                $imagen = new Imagen([
                    'url' => $path,
                    'extension' => $request->photo->extension()
                ]);

                $imagen->save();

                $currentuser->perfil->imagen()->attach($imagen);
            }


        } catch (\Exception $e) {
            //failed logic here
            DB::rollback();
            throw $e;
        }

        DB::commit();
    }
}
