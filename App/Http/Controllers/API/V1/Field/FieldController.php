<?php

namespace App\Http\Controllers\API\V1\Field;
use App\Http\Requests\Field\CreateFieldRequest;
use App\Http\Controllers\Controller;
use App\Services\FieldService;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    protected $fieldService;

    public function __construct(FieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function index(Request $request)
    {
        $fields = $this->fieldService->getAllFields($request->all());
        return response()->json([
            'status' => true,
            'data' => $fields
        ]);
    }

    public function show($id)
    {
        $field = $this->fieldService->getFieldDetails($id);
        return response()->json([
            'status' => true,
            'data' => $field
        ]);
    }


    public function store(CreateFieldRequest $request)
{
  
    $field = $this->fieldService->createField($request->validated());

    return response()->json([
        'status'  => true,
        'message' => 'Field created successfully',
        'data'    => $field
    ], 201); 
}


public function slots($id)
{
    $slots = $this->fieldService->getAvailableSlots($id);

    return response()->json([
        'status' => true,
        'data' => $slots
    ]);
}
}