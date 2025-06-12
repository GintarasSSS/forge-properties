<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertySearchRequest;
use App\Repositories\PropertyRepository;
use Illuminate\View\View;

class PropertySearchController extends Controller
{
    public function __construct(private readonly PropertyRepository $properties)
    {
    }

    public function index(PropertySearchRequest $request): View
    {
        $properties = $this->properties->search($request->validated());

        return view('property.search', compact('properties'));
    }
}
