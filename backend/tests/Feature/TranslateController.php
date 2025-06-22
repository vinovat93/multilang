<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslateController extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->language1 = Language::factory()->create([
            'language_name' => 'English',
            'language_code' => 'EN',
        ]);
        $this->language2 = Language::factory()->create([
            'language_name' => 'Spanish',
            'language_code' => 'ES',
        ]);

        $this->originalText1 = OriginalText::factory()->create([
            'text' => 'Hello World',
            'language_id' => $this->language1->id,
        ]);
        $this->originalText2 = OriginalText::factory()->create([
            'text' => 'Goodbye World',
            'language_id' => $this->language1->id,
        ]);

        $this->translation1 = Translations::factory()->create([
            'text_id' => $this->originalText1->id,
            'language_id' => $this->language2->id,
            'text' => 'Hola Mundo',
        ]);
    }

    public function test_index_returns_all_original_texts_with_translations()
    {
        $response = $this->getJson('/api/translations');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'texts' => [
                    '*' => [
                        'id',
                        'o_language_name',
                        'o_language_code',
                        'translation_id',
                        'text',
                        'original_text',
                        'language_id',
                    ],
                ],
            ])
            ->assertJsonFragment([
                'original_text' => 'Hello World',
                'translation_id' => $this->translation1->id,
                'text' => 'Hola Mundo',
                'o_language_code' => 'en',
            ])
            ->assertJsonFragment([
                'original_text' => 'Goodbye World',
                'translation_id' => null,
                'text' => null,
                'o_language_code' => 'en',
            ]);
    }

    public function test_index_returns_original_texts_filtered_by_language()
    {
        Translations::factory()->create([
            'text_id' => $this->originalText2->id,
            'language_id' => $this->language2->id,
            'text' => 'Adios Mundo',
        ]);

        $response = $this->getJson("/api/translations/{$this->language2->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'texts')
            ->assertJsonFragment([
                'original_text' => 'Hello World',
                'translation_id' => $this->translation1->id,
                'text' => 'Hola Mundo',
                'language_id' => $this->language2->id,
            ])
            ->assertJsonFragment([
                'original_text' => 'Goodbye World',
                'translation_id' => Translations::where('text_id', $this->originalText2->id)
                    ->where('language_id', $this->language2->id)->first()->id,
                'text' => 'Adios Mundo',
                'language_id' => $this->language2->id,
            ]);
    }

    public function test_store_creates_new_translation_successfully()
    {
        $newOriginalText = OriginalText::factory()->create([
            'text' => 'New text to translate',
            'language_id' => $this->language1->id,
        ]);

        $translationData = [
            'text_id' => $newOriginalText->id,
            'language_id' => $this->language2->id,
            'text' => 'Nuevo texto para traducir',
        ];

        $response = $this->postJson('/api/translations', $translationData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Text translation created successfully.',
                'text' => [
                    'text_id' => $newOriginalText->id,
                    'language_id' => $this->language2->id,
                    'text' => 'Nuevo texto para traducir',
                ],
            ]);
        $this->assertDatabaseHas('translations', $translationData);
    }

    public function test_store_returns_422_for_invalid_data()
    {
        $translationData = [
            'language_id' => $this->language2->id,
            'text_id' => $this->originalText1->id,
        ];

        $response = $this->postJson('/api/translations', $translationData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['text']);
    }

    public function test_store_returns_422_for_duplicate_translation()
    {
        $duplicateTranslationData = [
            'text_id' => $this->translation1->text_id,
            'language_id' => $this->translation1->language_id,
            'text' => 'Some other text for the same combination',
        ];

        $response = $this->postJson('/api/translations', $duplicateTranslationData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['text']);

    }

    public function test_update_modifies_translation_successfully()
    {
        $updatedText = 'Hello World - Updated';
        $updateData = ['text' => $updatedText];

        $response = $this->putJson("/api/translations/{$this->translation1->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Text translation updated successfully.',
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $this->translation1->id,
            'text' => $updatedText,
        ]);
    }

    public function test_update_returns_422_for_invalid_data()
    {
        $updateData = ['text' => ''];

        $response = $this->putJson("/api/translations/{$this->translation1->id}", $updateData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['text']);
    }

    public function test_update_returns_422_for_duplicate_text()
    {
        $anotherTranslation = Translations::factory()->create([
            'text_id' => OriginalText::factory()->create(['language_id' => $this->language1->id])->id,
            'language_id' => $this->language2->id,
            'text' => 'Existing Unique Text',
        ]);

        $updateData = ['text' => $anotherTranslation->text];

        $response = $this->putJson("/api/translations/{$this->translation1->id}", $updateData);

        $conflictingTranslation = Translations::factory()->create([
            'text_id' => OriginalText::factory()->create(['language_id' => $this->language1->id])->id,
            'language_id' => Language::factory()->create()->id,
            'text' => 'This is a unique text.',
        ]);

        $updateData = ['text' => $conflictingTranslation->text];

        $response = $this->putJson("/api/translations/{$this->translation1->id}", $updateData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['text']);
    }

    public function test_destroy_deletes_translation_successfully()
    {
        $translationToDeleteId = $this->translation1->id;

        $response = $this->deleteJson("/api/translations/{$translationToDeleteId}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('translations', ['id' => $translationToDeleteId]);
    }

    public function test_destroy_returns_404_for_non_existent_translation()
    {
        $nonExistentId = 99999; // An ID that surely doesn't exist

        $response = $this->deleteJson("/api/translations/{$nonExistentId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
