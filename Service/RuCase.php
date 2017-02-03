<?php

namespace Etfostra\RuStringsBundle\Service;
use Doctrine\Common\Cache\RedisCache;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Склоенение существительных в разные падежи
 * http://opencorpora.org/dict.php?act=gram
 *
 * Class RuCase
 * @package Tradeins\CorpBundle\Service
 */
class RuCase
{
    /** @type  RedisCache */
    protected $redis;

    /** @var int Время жизни кеша */
    protected $ttl;

    /** @var string pyphrasy api url */
    protected $api_url;


    /**
     * RuCase constructor.
     * @param RedisCache $redis
     * @param ContainerInterface $container
     */
    public function __construct(RedisCache $redis, ContainerInterface $container)
    {
        $this->redis    = $redis;
        $this->ttl      = $container->getParameter('etfostra_ru_strings.redis_cache_ttl');
        $this->api_url  = $container->getParameter('etfostra_ru_strings.pyphrasy_api_url');
    }

    /**
     * @param $phrase string
     * @param $form string http://opencorpora.org/dict.php?act=gram
     * @param $ttl int
     * @return bool|string
     */
    public function inflect($phrase, $form, $ttl = null)
    {
        if ($ttl === null) {
            $ttl = $this->ttl;
        }

        $ttl = (int) $ttl;

        $cacheKey = 'RuCase::inflect:'.md5($phrase.':'.$form);

        $data = $this->getRedis()->fetch($cacheKey);

        if (empty($data)) {
            $data = $this->query($phrase, $form);
            $this->getRedis()->save($cacheKey, $data, $ttl);
        }

        return $data;
    }

    /**
     * @param $word string
     * @param $case string http://opencorpora.org/dict.php?act=gram
     * @param int $attempt
     * @return string
     */
    private function query($word, $case, $attempt = 1)
    {
        try {
            $options = array(
                'phrase' => $word,
                'forms' => $case
            );
            $url = $this->api_url.'?'.http_build_query($options);

            $json = file_get_contents($url);
            $array = json_decode($json, true);

            $orig = $array['orig'];
            $infl = $array[$case];

            $origLen = mb_strlen($orig, 'UTF-8');
            $inflLen = mb_strlen($infl, 'UTF-8');

            $inflection = $infl;

            for ($i = 0; $i < $origLen; $i++) {
                $letter = mb_substr($orig, $i, 1, 'UTF-8');
                $ucLetter = mb_strtoupper($letter, 'UTF-8');

                if ($letter === $ucLetter) {

                    for ($j = 0; $j < 3; $j++) {
                        $inflLetter = mb_substr($inflection, $i + $j, 1, 'UTF-8');
                        $ucInflLetter = mb_strtoupper($inflLetter, 'UTF-8');

                        if ($ucInflLetter === $ucLetter) {
                            $inflection = mb_substr($inflection, 0, $i + $j, 'UTF-8').$ucInflLetter.mb_substr($inflection, $i + $j + 1, $inflLen, 'UTF-8');
                            break;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $attempt++;
            if ($attempt > 4) {
                $inflection = $word;
            } else {
                $inflection = $this->query($word, $case, $attempt);
            }
        }

        return mb_ereg_replace("[^А-Яа-я\- ]", "", $inflection);
    }

    /**
     * @return RedisCache
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @param RedisCache $redis
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }
}
