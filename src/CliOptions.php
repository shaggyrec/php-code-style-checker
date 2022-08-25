<?php

namespace Shaggyrec\CodeStyleChecker;

use Shaggyrec\CodeStyleChecker\Exception\ConflictOptions;
use Shaggyrec\CodeStyleChecker\Exception\MandatoryOption;

class CliOptions
{
    // available script options
    private const OPTION_HELP = 'help';
    private const OPTION_SRC = 'src';
    public const OPTION_DIFF = 'diff';
    private const OPTION_VERBOSE = 'verbose';
    private const OPTION_LOG_TO = 'log';
    private const OPTION_STANDARD = 'standard';
    private const OPTION_DEBUG = 'debug';
    private const OPTION_COLORS = 'colors';

    /**
     * @var array of options, passed to cli script
     */
    private array $options;

    /**
     * @return CliOptions
     */
    public static function fromEnv(): CliOptions
    {
        return new self(
            getopt(
                '',
                [
                    self::OPTION_HELP,
                    self::OPTION_SRC . ':',
                    self::OPTION_DIFF . ':',
                    self::OPTION_VERBOSE,
                    self::OPTION_LOG_TO . ':',
                    self::OPTION_STANDARD . ':',
                    self::OPTION_DEBUG,
                    self::OPTION_COLORS,
                ]
            )
        );
    }

    /**
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * @param string $option
     * @return bool
     */
    public function hasOption(string $option): bool
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * @return bool
     */
    public function isHelp(): bool
    {
        return $this->hasOption(self::OPTION_HELP);
    }

    /**
     * @return string
     */
    public function src(): string
    {
        return $this->options[self::OPTION_SRC];
    }

    /**
     * @return string|null
     */
    public function diff(): ?string
    {
        return $this->options[self::OPTION_DIFF] ?? null;
    }

    /**
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->options[self::OPTION_VERBOSE] ?? false;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->hasOption(self::OPTION_DEBUG);
    }

    public function isColors(): bool
    {
        return $this->hasOption(self::OPTION_COLORS);
    }

    /**
     * @return string|null
     */
    public function logTo(): ?string
    {
        return $this->options[self::OPTION_LOG_TO] ?? null;
    }

    /**
     * @return string|null
     */
    public function standard(): ?string
    {
        return $this->options[self::OPTION_STANDARD] ?? __DIR__ . '/Standards/FF/ruleset.xml';
    }

    /**
     * @throws ConflictOptions
     * @throws MandatoryOption
     * @return void
     */
    public function assertMandatoryOptions(): void
    {
        if (!isset($this->options[self::OPTION_SRC])  && !isset($this->options[self::OPTION_DIFF])) {
            throw new MandatoryOption('One of option ' . self::OPTION_SRC . ' or ' . self::OPTION_DIFF .  ' is mandatory');
        }

        if (isset($this->options[self::OPTION_SRC], $this->options[self::OPTION_DIFF])) {
            throw new ConflictOptions('You can use either ' . self::OPTION_SRC . ' or ' . self::OPTION_DIFF);
        }
    }
}
