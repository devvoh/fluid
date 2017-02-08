<?php

namespace Parable\Console;

class Output
{
    /** @var array */
    protected $tags = [
        /* foreground colors */
        'default' => "\e[0m",
        'black'   => "\e[0;30m",
        'red'     => "\e[0;31m",
        'green'   => "\e[0;32m",
        'yellow'  => "\e[0;33m",
        'blue'    => "\e[0;34m",
        'purple'  => "\e[0;35m",
        'cyan'    => "\e[0;36m",
        'white'   => "\e[0;37m",

        /* background colors */
        'black_bg'     => "\e[40m",
        'red_bg'       => "\e[41m",
        'green_bg'     => "\e[42m",
        'yellow_bg'    => "\e[43m",
        'blue_bg'      => "\e[44m",
        'magenta_bg'   => "\e[45m",
        'cyan_bg'      => "\e[46m",
        'lightgray_bg' => "\e[47m",

        /* styles */
        'error'   => "\e[0;37m\e[41m",
        'success' => "\e[0;30m\e[42m",
        'info'    => "\e[0;30m\e[43m",
    ];

    /**
     * @param string $string
     *
     * @return $this
     */
    public function write($string)
    {
        $string = $this->parseTags($string);
        echo $string;
        return $this;
    }

    /**
     * @param array|string $lines
     *
     * @return $this
     */
    public function writeln($lines)
    {
        if (!is_array($lines)) {
            $lines = [$lines];
        }

        foreach ($lines as $line) {
            $this->write($line);
            $this->newline();
        }
        return $this;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function newline($count = 1)
    {
        echo str_repeat(PHP_EOL, $count);
        return $this;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function writeError($string)
    {
        $this->writeBlock($string, 'error');
        return $this;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function writeInfo($string)
    {
        $this->writeBlock($string, 'info');
        return $this;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function writeSuccess($string)
    {
        $this->writeBlock($string, 'success');
        return $this;
    }

    /**
     * @param string $string
     * @param string $tag
     *
     * @return $this
     */
    public function writeBlock($string, $tag = 'info')
    {
        $strlen = mb_strlen($string);

        $this->writeln([
            "",
            " <{$tag}>┌" . str_repeat("─", $strlen + 2) . "┐</{$tag}>",
            " <{$tag}>│ {$string} │</{$tag}>",
            " <{$tag}>└" . str_repeat("─", $strlen + 2) . "┘</{$tag}>",
            "",
        ]);
        return $this;
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    public function parseTags($string)
    {
        foreach ($this->tags as $tag => $code) {
            if (
                strpos($string, "<{$tag}>") !== false
                || strpos($string, "</{$tag}>") !== false
            ) {
                $string = str_replace("<{$tag}>", $code, $string);
                $string = str_replace("</{$tag}>", $this->tags['default'], $string);
            }
        }

        return $string . $this->tags['default'];
    }
}
