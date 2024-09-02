<?php

namespace Jdsantos\Laradocker\Helpers;

/**
 * Helper class to handle common stub operations
 */
class StubFileHelper
{
    /**
     * This method replaces a specific line in a file, that
     * contains a specific $searchWord, by a $replacementString
     *
     * @param  mixed  $filePath
     * @param  mixed  $searchWord
     * @param  mixed  $replacementString
     * @return [type]
     */
    public static function replaceLineInFile($filePath, $searchWord, $replacementString)
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
