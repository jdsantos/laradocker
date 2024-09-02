<?php

namespace Jdsantos\Laradocker\Helpers;

class StubFile
{

public static function replaceLineWithWordInFile($filePath, $searchWord, $replacementString)
    {
        // Read the entire file into an array of lines
        $fileContents = file($filePath);

        // Iterate through each line
        foreach ($fileContents as &$line) {
            // Check if the line contains the search word
            if (strpos($line, $searchWord) !== false) {
                // Replace the entire line with the replacement string
                $line = $replacementString.PHP_EOL;
            }
        }

        return implode('', $fileContents);
    }
}