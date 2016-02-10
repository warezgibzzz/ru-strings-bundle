<?php

namespace Etfostra\RuStringsBundle\Twig;

use Etfostra\RuStringsBundle\Service\RuCase;
use Etfostra\RuStringsBundle\Service\RuPlural;

/**
 * Class RuStringsExtension
 * @package Etfostra\RuStringsBundle\Twig
 */
class RuStringsExtension extends \Twig_Extension
{
    /** @var RuCase */
    protected $ru_case;

    /** @var  RuPlural */
    protected $ru_plural;

    /**
     * RuStringsExtension constructor.
     * @param RuCase $ru_case
     * @param RuPlural $ru_plural
     */
    public function __construct(RuCase $ru_case, RuPlural $ru_plural)
    {
        $this->ru_case = $ru_case;
        $this->ru_plural = $ru_plural;
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
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('plural', array($this, 'PluralFunction')),
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
        return $this->getRuCase()->inflect($phrase, $form);
    }

    /**
     * @param $n int Число для согласования
     * @param $form0 string Форма фразы согласованная с нолем
     * @param $form1 string Форма фразы согласованная с единицей
     * @param $form2 string Форма фразы согласованная с двойкой
     * @return string
     */
    public function PluralFunction($n, $form0, $form1, $form2)
    {
        return $this->getRuPlural()->plural($n, $form0, $form1, $form2);
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

    /**
     * @return RuPlural
     */
    public function getRuPlural()
    {
        return $this->ru_plural;
    }

    /**
     * @param RuPlural $ru_plural
     */
    public function setRuPlural($ru_plural)
    {
        $this->ru_plural = $ru_plural;
    }
}