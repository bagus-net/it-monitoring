<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function index()
    {
        $items = Manufacturer::orderBy('name')->get();
        return view('masters.manufacturers.index', compact('items'));
    }

    public function create()
    {
        return view('masters.manufacturers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:255','country'=>'nullable|string|max:255','notes'=>'nullable|string']);
        Manufacturer::create($data);
        return redirect()->route('masters.manufacturers.index')->with('success','Manufacturer added');
    }

    public function edit(Manufacturer $manufacturer)
    {
        return view('masters.manufacturers.edit', compact('manufacturer'));
    }

    public function update(Request $request, Manufacturer $manufacturer)
    {
        $data = $request->validate(['name'=>'required|string|max:255','country'=>'nullable|string|max:255','notes'=>'nullable|string']);
        $manufacturer->update($data);
        return redirect()->route('masters.manufacturers.index')->with('success','Manufacturer updated');
    }

    public function destroy(Manufacturer $manufacturer)
    {
        $manufacturer->delete();
        return back()->with('success','Deleted');
    }
}
