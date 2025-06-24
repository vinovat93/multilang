<?php

namespace App\Console\Commands;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory; // For reading and writing documents
use PhpOffice\PhpWord\SimpleType\TblWidth;

use Illuminate\Console\Command;

class ProcessOffice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    const IS_INDEX = 1;
    const CLI = true;
    const SCRIPT_FILENAME = "test";

    protected $signature = 'app:process-office';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

     public static function addColoredTextToCell($cell, $text, $defaultFontSize, $targetLang, $color = '00FF00') {

         $newText = '';
         for ($i=0;$i<strlen($text);$i++) {
             $openPos = strpos($text, '{{', $i);
             $closePos = strpos($text, '}}', $i);

             if ($openPos !== false && $closePos !== false) {
                 $defaultText = substr($text, $i, $openPos - $i);;
                 $newText .= $defaultText;
                 $tagText = substr($text, $openPos, $closePos - $openPos + 2);
                 $newText .= $tagText;
                 $cell->addText(
                     $defaultText,
                     ['size' => $defaultFontSize, 'rtl' => !!$targetLang["isRTL"], 'bidi' => !!$targetLang["isRTL"]],
                     $targetLang["isRTL"] ? ['align' => 'right', 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT] : null
                 );
                 // Add the colored text
                 $cell->addText(
                     $tagText,
                     ['size' => $defaultFontSize, 'color' => $color, 'rtl' => !!$targetLang["isRTL"], 'bidi' => !!$targetLang["isRTL"]],
                     $targetLang["isRTL"] ? ['align' => 'right', 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT] : null
                 );
                 $i = $closePos + 1;
             } else {
                 $defaultText = substr($text, $i);
                 $cell->addText(
                     $defaultText,
                     ['size' => $defaultFontSize, 'rtl' => !!$targetLang["isRTL"], 'bidi' => !!$targetLang["isRTL"]],
                     $targetLang["isRTL"] ? ['align' => 'right', 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT] : null
                 );
                 $newText .= $defaultText;
                 break;
             }
         }
     }
     public static function getEndingNotes(array $writers, string $filename): string
     {
         $result = '';

         // Do not show execution time for index
         if (!self::IS_INDEX) {
             $result .= date('H:i:s') . ' Done writing file(s)' . '\n';
             $result .= date('H:i:s') . ' Peak memory usage: ' . (memory_get_peak_usage(true) / 1024 / 1024) . ' MB' . '\n';
         }

         // Return
         if (self::CLI) {
             $result .= 'The results are stored in the "results" subdirectory.' . '\n';
         } else {
             if (!self::IS_INDEX) {
                 $types = array_values($writers);
                 $result .= '<p>&nbsp;</p>';
                 $result .= '<p>Results: ';
                 foreach ($types as $type) {
                     if (null !== $type) {
                         $resultFile = 'results/' . self::SCRIPT_FILENAME . '.' . $type;
                         if (file_exists($resultFile)) {
                             $result .= "<a href='{$resultFile}' class='btn btn-primary'>{$type}</a> ";
                         }
                     }
                 }
                 $result .= '</p>';

                 $result .= '<pre>';
                 if (file_exists($filename . '.php')) {
                     $result .= highlight_file($filename . '.php', true);
                 }
                 $result .= '</pre>';
             }
         }

         return $result;
     }

     public static function write(PhpWord $phpWord, string $filename, array $writers): string
     {
         $result = '';

         // Write documents
         foreach ($writers as $format => $extension) {
             $result .= date('H:i:s') . " Write to {$format} format";
             if (null !== $extension) {
                 $targetFile = __DIR__ . "/results/{$filename}.{$extension}";
                 $phpWord->save($targetFile, $format);
             } else {
                 $result .= ' ... NOT DONE!';
             }
             $result .= '\n';
         }

         $result .= self::getEndingNotes($writers, $filename);

         return $result;
     }


    public function handle()
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $header = ['size' => 16, 'bold' => true];
        $section->addText('Basic table', $header);
        $table = $section->addTable();
        $table->addRow(100);
        $writers = ['Word2007' => 'docx'];
        $tableCellRTLStyle = ['borderSize' => 6, 'borderColor' => 'ff5733'];
        $tableCellStyle = ['borderSize' => 6, 'borderColor' => '999999'];

        $lightGray = "d3d3d3";
        $defaultFontSize = 14;

        $sourceText = "ABC {{dsadasdasddsa}}";
        $translationText = "sdadsada2312312321";
        $segment = [
            "isRepetition"=>false
        ];

        $sourceLang = [
            "isRTL"=>true
        ];

        $targetLang = [
            "isRTL"=>true
        ];

        if ($segment["isRepetition"]) {
            $table->addCell(4000, $sourceLang["isRTL"] ? $tableCellRTLStyle : $tableCellStyle)
                ->addText(
                    $sourceText,
                    [
                        'color' => $lightGray,
                        'size' => $defaultFontSize,
                        'rtl' => !!$sourceLang["isRTL"],
                        'bidi' => !!$sourceLang["isRTL"]
                    ],
                    $sourceLang["isRTL"] ? ['align' => 'right', 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT] : null
                );
        } else {
            $cell = $table->addCell(4000, $sourceLang["isRTL"] ? $tableCellRTLStyle : $tableCellStyle);

            $paragraph = $cell->addTextRun();

            self::addColoredTextToCell($paragraph, $sourceText, $defaultFontSize, $sourceLang, 'D3D3D3');
        }

        // Target
        // If repetition show target text as light gray
        if ($segment["isRepetition"]) {
            $table
                ->addCell(4000, $targetLang["isRTL"] ? $tableCellRTLStyle : $tableCellStyle)
                ->addText(
                    $translationText,
                    [
                        'color' => $lightGray,
                        'size' => $defaultFontSize,
                        'rtl' => !!$targetLang["isRTL"],
                        'bidi' => !!$targetLang["isRTL"]
                    ],
                    $targetLang["isRTL"] ? ['align' => 'right', 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT] : null
                );
        } else {
            $cell = $table
                ->addCell(4000, $targetLang["isRTL"] ? $tableCellRTLStyle : $tableCellStyle);

            $paragraph = $cell->addTextRun();

            self::addColoredTextToCell($paragraph, $translationText, $defaultFontSize, $targetLang, 'D3D3D3');
        }

        self::write($phpWord, basename(__FILE__, '.php'), $writers);
    }
}
