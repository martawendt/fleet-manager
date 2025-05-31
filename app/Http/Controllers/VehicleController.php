<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $vehicles = [
        [
            'id' => 1,
            'brand_model' => 'Toyota Corolla',
            'year' => 2018,
            'vin' => 'JH4KA4650MC012345',
            'plate' => 'KR123AB',
            'inspection_date' => '2024-12-01',
            'insurance_date' => '2024-11-15',
        ],
        [
            'id' => 2,
            'brand_model' => 'Ford Focus',
            'year' => 2020,
            'vin' => 'WVWZZZ1JZXW000123',
            'plate' => 'WE456CD',
            'inspection_date' => '2025-01-20',
            'insurance_date' => '2024-12-30',
        ],
        [
            'id' => 3,
            'brand_model' => 'Skoda Octavia',
            'year' => 2017,
            'vin' => 'VF1BB1CB637654321',
            'plate' => 'GD789EF',
            'inspection_date' => '2024-10-10',
            'insurance_date' => '2024-09-30',
        ],
    ];

    return Inertia::render('Vehicles/Index', [
        'vehicles' => $vehicles
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        //
    }
}
