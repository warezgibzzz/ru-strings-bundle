<?php

namespace Etfostra\RuStringsBundle\Service;
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
    /** @type  \Redis */
    protected $redis;

    /** @var int Время жизни кеша */
    protected $ttl;

    /** @var string pyphrasy api url */
    protected $api_url;


    /**
     * RuCase constructor.
     * @param \Redis $redis
     * @param ContainerInterface $container
     */
    public function __construct(\Redis $redis, ContainerInterface $container)
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

        $cacheKey = __CLASS__.':'.__METHOD__.':'.md5($phrase.':'.$form);

        $data = $this->getRedis()->get($cacheKey);

        if (empty($data)) {
            $data = $this->query($phrase, $form);
            $this->getRedis()->setex($cacheKey, $ttl, $data);
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

                if ($letter === mb_strtoupper($letter, 'UTF-8')) {
                    $inflLetter = mb_substr($inflection, $i, 1, 'UTF-8');
                    $inflLetter = mb_strtoupper($inflLetter, 'UTF-8');

                    $inflection = mb_substr($inflection, 0, $i, 'UTF-8').$inflLetter.mb_substr($inflection, $i+1, $inflLen, 'UTF-8');
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

        return (string)$inflection;
    }

    /**
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @param \Redis $redis
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }
}
