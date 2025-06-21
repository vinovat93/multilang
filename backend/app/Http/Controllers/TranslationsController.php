<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Translations;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Models\Languages;
use App\Models\OriginalTexts;

class TranslationsController extends Controller
{

    public function index(Request $request, Languages $language)
    {
        return response()->json(['texts' => OriginalTexts::leftJoin('translations', function ($join) use ($language) {
           $join->on('translations.text_id', '=', 'texts.id')->where('translations.language_id', '=', $language->id);
        })->get()], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = Validator::make($request->post(), [
                'text' => 'required|string|unique:translations,text,text_id,language_id',
                'language_id' => 'required|exists:languages,id',
                'text_id' => 'required|numeric|unique:texts,text,language_id'
            ]);
            if ($validatedData->fails()) {
                $errors = $validatedData->errors();
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $errors
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $text = Translations::create($validatedData->validated());
            return response()->json(['message' => 'Text translation created successfully.','text' => $text], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
             return response()->json([
                 'error' => 'Validation failed: Invalid Text translation data.',
                 'details' => $e->errors(),
                 'status_code' => Response::HTTP_NOT_FOUND,
             ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, Translations $translation) // Route model binding example
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'text' => 'required|string|unique:translations,text,text_id,language_id'
            ]);

            if ($validatedData->fails()) {
                $errors = $validatedData->errors();
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $errors
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $text = $translation->update($validatedData->validated());
            return response()->json(['message' => 'Text translation updated successfully.'], Response::HTTP_OK);
        } catch (ValidationException $e) {
             return response()->json([
                 'error' => 'Validation failed: Invalid Text translation data.',
                 'details' => $e->errors(),
                 'status_code' => Response::HTTP_NOT_FOUND,
             ], Response::HTTP_NOT_FOUND);
        }
    }

        public function destroy(Request $request, Translations $translation) // Route model binding example
        {
            $translation->delete();
            return response()->json(['message' => 'Text translation updated successfully.'], Response::HTTP_NO_CONTENT);

        }

}
