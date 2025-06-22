<?php

namespace App\Http\Controllers;

use App\Models\Languages;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class LanguagesController extends Controller
{
    public function index(Request $request, Languages $language)
    {
        return response()->json(['texts' => Languages::all()], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = Validator::make($request->post(), [
                'language_code' => 'required|string|unique:languages|max:100',
                'language_name' => 'required|string|max:100',
            ]);

            if ($validatedData->fails()) {
                $errors = $validatedData->errors();

                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $errors,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $language = Languages::create($validatedData->validated());

            return response()->json(['message' => 'Language created successfully.', 'language' => $language], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed: Invalid language data.',
                'details' => $e->errors(), // This contains the actual validation error messages
                'status_code' => Response::HTTP_NOT_FOUND, // Setting status to 404
            ], Response::HTTP_NOT_FOUND); // Send 404 HTTP status
        }
    }
}
