<?php

namespace Mindgruve\Gruver\Factory;

use Mindgruve\Gruver\Config\GruverConfig;
use Symfony\Component\Yaml\Yaml;

class UrlFactory
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var GruverConfig
     */
    protected $config;

    public function __construct(GruverConfig $config)
    {
        $this->config = $config;
        $this->options = array();

        $yaml = new Yaml();
        $this->options = array_merge($this->options, $yaml->parse(__DIR__.'/../Resources/data/fixture-animals.yml'));
        $this->options = array_merge($this->options, $yaml->parse(__DIR__.'/../Resources/data/fixture-colors.yml'));
    }

    public function generate($pattern)
    {
        $url = $pattern;

        $url = str_replace('%color%', $this->randomElmt('colors'), $url);
        $url = str_replace('%animal%', $this->randomElmt('animals'), $url);
        $url = str_replace('%date%', date($this->config->get('[config][date_format]')), $url);

        return $url;
    }

    public function randomElmt($key)
    {
        $rand = array_rand($this->options[$key]);

        return $this->options[$key][$rand];
    }
}
