<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;
use function gettype;
use function is_string;
use function preg_match;
use function sprintf;

final class UuidMatcher extends Matcher
{
    const PATTERN = 'uuid';

    const UUID_PATTERN = '[\da-f]{8}-[\da-f]{4}-[1-5][\da-f]{3}-[89ab][\da-f]{3}-[\da-f]{12}';

    const UUID_FORMAT_PATTERN = '|^'.self::UUID_PATTERN.'$|';

    /**
     * @var Backtrace
     */
    private $backtrace;

    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->parser = $parser;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!is_string($value)) {
            $this->error = sprintf(
                '%s "%s" is not a valid UUID: not a string.',
                gettype($value),
                new StringConverter($value)
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (1 !== preg_match(self::UUID_FORMAT_PATTERN, $value)) {
            $this->error = sprintf(
                '%s "%s" is not a valid UUID: invalid format.',
                gettype($value),
                $value
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        if (!is_string($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        $result = $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::PATTERN);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
