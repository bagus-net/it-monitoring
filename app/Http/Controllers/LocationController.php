<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $items = Location::orderBy('name')->get();
        return view('masters.locations.index', compact('items'));
    }

    public function create()
    {
        return view('masters.locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:255','address'=>'nullable|string|max:255','floor'=>'nullable|string|max:50']);
        Location::create($data);
        return redirect()->route('masters.locations.index')->with('success','Location added');
    }

    public function edit(Location $location)
    {
        return view('masters.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate(['name'=>'required|string|max:255','address'=>'nullable|string|max:255','floor'=>'nullable|string|max:50']);
        $location->update($data);
        return redirect()->route('masters.locations.index')->with('success','Location updated');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return back()->with('success','Deleted');
    }
}
