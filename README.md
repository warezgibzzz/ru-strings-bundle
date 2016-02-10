# ru-strings-bundle
Склонение по падежам и сопряжение с числом в twig + сервис для Symfony2. Для кеширования используется Redis.
## Пример
``` twig
{% set auto_count = 223 %}
{% set surface = 'белый снег' %}
Проехали {{ auto_count }} {{ plural(auto_count, 'автомобилей', 'автомобиль', 'автомобиля') }} по {{ surface | inflect('datv') }}

{# Проехали 223 автомобиля по белому снегу #}
```

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

### Склонение по падежам в Twig
``` twig
{{ 'белый снег' | inflect('gent') }} {# белого снега #}
{{ 'белый снег' | inflect('datv') }} {# белому снегу #}
{{ 'белый снег' | inflect('accs') }} {# белый снег #}
{{ 'белый снег' | inflect('ablt') }} {# белым снегом #}
{{ 'белый снег' | inflect('loct') }} {# белом снеге #}
{{ 'белый снег' | inflect('voct') }} {# белый снег #}
```

Полный список опций склонения http://opencorpora.org/dict.php?act=gram

### Согласование с числом
``` php
$pluralizer = $this->get('ru_strings.plural');

$pluralizer->plural(101, 'автомобилей', 'автомобиль', 'автомобиля'); //автомобиль
$pluralizer->plural(102, 'автомобилей', 'автомобиль', 'автомобиля'); //автомобиля
$pluralizer->plural(100, 'автомобилей', 'автомобиль', 'автомобиля'); //автомобилей
```

### Согласование с числом в Twig
``` twig
{{ plural(101, 'автомобилей', 'автомобиль', 'автомобиля') }} {# автомобиль #}
{{ plural(102, 'автомобилей', 'автомобиль', 'автомобиля') }} {# автомобиля #}
{{ plural(100, 'автомобилей', 'автомобиль', 'автомобиля') }} {# автомобилей #}
```

Список тернарных операторов для других языков http://www.gnu.org/software/gettext/manual/html_mono/gettext.html#Plural-forms

Метод `plural` не использует кеширование. 