<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;
use function is_array;
use function count;
use function sprintf;

final class Count implements PatternExpander
{
    public const NAME = 'count';

    use BacktraceBehavior;

    /**
     * @var null|string
     */
    private $error;

    /**
     * @var int
     */
    private $value;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function match($value) :bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!is_array($value)) {
            $this->error = sprintf('Count expander require "array", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (count($value) !== $this->value) {
            $this->error = sprintf('Expected count of %s is %s.', new StringConverter($value), new StringConverter($this->value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    public function getError() : ?string
    {
        return $this->error;
    }
}
