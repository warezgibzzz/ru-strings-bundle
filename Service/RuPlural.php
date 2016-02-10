<?php

namespace Etfostra\RuStringsBundle\Service;

/**
 * Class RuPlural
 * @package Etfostra\RuStringsBundle\Service
 */
class RuPlural
{
    /**
     * @param $n int Число для согласования
     * @param $form0 string Форма фразы согласованная с нолем
     * @param $form1 string Форма фразы согласованная с единицей
     * @param $form2 string Форма фразы согласованная с двойкой
     * @return string
     */
    public function plural($n, $form0, $form1, $form2)
    {
        $n = (int)$n;
        return $n%10==1&&$n%100!=11 ? $form1 : ($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20) ? $form2 : $form0);
    }
}
