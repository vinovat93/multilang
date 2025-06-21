<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OriginalTexts;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class OriginalTextsController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = Validator::make($request->post(), [
                'text' => 'required|string|unique:texts,text,language_id',
                'language_id' => 'required|exists:languages,id'
            ]);

            if ($validatedData->fails()) {
                $errors = $validatedData->errors();
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $errors
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $text = OriginalTexts::create($validatedData->validated());
            return response()->json(['message' => 'Text created successfully.','text' => $text], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
             return response()->json([
                 'error' => 'Validation failed: Invalid Text data.',
                 'details' => $e->errors(),
                 'status_code' => Response::HTTP_NOT_FOUND,
             ], Response::HTTP_NOT_FOUND);
        }
    }
}
