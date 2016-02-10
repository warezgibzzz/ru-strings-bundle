<?php

namespace Etfostra\RuStringsBundle\Twig;

use Etfostra\RuStringsBundle\Service\RuCase;

/**
 * Class RuStringsExtension
 * @package Etfostra\RuStringsBundle\Twig
 */
class RuStringsExtension extends \Twig_Extension
{
    /** @var RuCase */
    protected $ru_case;

    /**
     * RuStringsExtension constructor.
     * @param RuCase $ru_case
     */
    public function __construct(RuCase $ru_case)
    {
        $this->ru_case = $ru_case;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('inflect', array($this, 'InflectFilter')),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ru_strings_extension';
    }

    /**
     * @param $phrase string
     * @param $form string
     * @return bool|string
     */
    public function InflectFilter($phrase, $form)
    {
        $inflected_phrase = $this->getRuCase()->inflect($phrase, $form);

        return $inflected_phrase;
    }

    /**
     * @return RuCase
     */
    public function getRuCase()
    {
        return $this->ru_case;
    }

    /**
     * @param RuCase $ru_case
     */
    public function setRuCase(RuCase $ru_case)
    {
        $this->ru_case = $ru_case;
    }
}