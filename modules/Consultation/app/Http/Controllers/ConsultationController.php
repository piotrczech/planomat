<?php

declare(strict_types=1);

namespace Modules\Consultation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('consultation::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('consultation::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('consultation::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('consultation::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): void
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): void
    {
    }
}
