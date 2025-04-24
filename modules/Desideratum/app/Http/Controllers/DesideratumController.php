<?php

declare(strict_types=1);

namespace Modules\Desideratum\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DesideratumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('desideratum::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('desideratum::create');
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
        return view('desideratum::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('desideratum::edit');
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
