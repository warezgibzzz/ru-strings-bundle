# ru-strings-bundle
Склонение по падежам и сопряжение с числом в twig + сервис для Symfony2. Для кеширования используется Redis.


## Установка
``` bash
composer require it-blaster/ru-strings-bundle
```

Добавить в `AppKernel.php`
``` php
<?php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Etfostra\RuStringsBundle\EtfostraRuStringsBundle(),
    );
}
```

## Настройка
Доступные параметры в `config.yml`:
``` yaml
etfostra_ru_strings:
    redis_cache_ttl: 2592000 #Время жизни кеша в секундах
    pyphrasy_api_url: https://pyphrasy.herokuapp.com/inflect #API URL 
```

## Примеры использования
### Склонение по падежам
``` php
$inflector = $this->get('ru_strings.case');

$inflector->inflect('белый снег', 'gent'); //белого снега
$inflector->inflect('белый снег', 'datv'); //белому снегу
$inflector->inflect('белый снег', 'accs'); //белый снег
$inflector->inflect('белый снег', 'ablt'); //белым снегом
$inflector->inflect('белый снег', 'loct'); //белом снеге
$inflector->inflect('белый снег', 'voct'); //белый снег
```

### В Twig
``` twig
{{ 'белый снег' | inflect('gent') }} {# белого снега #}
{{ 'белый снег' | inflect('datv') }} {# белому снегу #}
{{ 'белый снег' | inflect('accs') }} {# белый снег #}
{{ 'белый снег' | inflect('ablt') }} {# белым снегом #}
{{ 'белый снег' | inflect('loct') }} {# белом снеге #}
{{ 'белый снег' | inflect('voct') }} {# белый снег #}
```

Полный список опций склонения http://opencorpora.org/dict.php?act=gram