<?php

namespace Shaggyrec\CodeStyleChecker\Standards\FF\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class MultilineTernaryOperatorSniff implements Sniff
{
    /**
     * @return array
     */
    public function register(): array
    {
        return [
            T_INLINE_ELSE,
            T_INLINE_THEN,
        ];
    }

    /**
     * @param File $phpcsFile
     * @param $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        for ($i = 1;; $i++) {
            if ($tokens[$stackPtr - $i]['type'] !== 'T_WHITESPACE') {
                $phpcsFile->addError(
                    sprintf(
                        'Expected newline before "%s" part of ternary operator',
                        $tokens[$stackPtr]['content'],
                    ),
                    $stackPtr,
                    '',
                );
                break;
            }

            if ($tokens[$stackPtr - $i]['content'] === "\n") {
                break;
            }
        }
    }
}
