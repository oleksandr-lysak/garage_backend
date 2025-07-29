<?php

return [

    /**
     * @see https://turbosms.ua/route.html
     * Поточний токен HTTP API авторизації
     * Видається після реєстрації на сторінці налаштувань шлюза
     */
    'test_mode' => env('TURBOSMS_TEST_MODE', true),

    /**
     * @see https://turbosms.ua/route.html
     * Поточний токен HTTP API авторизації
     * Видається після реєстрації на сторінці налаштувань шлюза
     */
    'api_key' => env('TURBOSMS_API_KEY', 'test_api_key'),

    /**
     * @see https://turbosms.ua/sign.html
     * Можна зареєструвати власного відправника, з якого буде приходити SMS
     * Реєстрація безкоштовна но займає продовжуваний час
     * Можна використовувати вже готових
     *
     * Supported: "MAGAZIN", "Market", "TAXI", "SERVIS TAXI",
     *            "Dostavka24", "IT Alarm", "Post Master"
     */
    'sms_sender' => env('TURBOSMS_SMS_SENDER', 'Market'),

    /**
     * @see https://turbosms.ua/viber/show/senders.html
     * Можна зареєструвати власного відправника,
     * Реєстрація безкоштовна, довга і можливо буде мінімальний ліміт в місяць
     *
     * Supported: "Mobibon"
     */
    'viber_sender' => env('TURBOSMS_VIBER_SENDER', 'Mobibon'),

    /**
     * Вимкнене значення дозволяє відправляти повідомлення в будь-який час дня
     * Встановивши "true" - параметр $start_time змінюється за умовою "min_hour" і "max_hour".
     */
    'sleep_mode' => false,

    /**
     * Мінімальний і максимальний час дозволеної відправки повідомлення
     * Перетворення часу зачіпає тільки години, хвилини залишаються ті ж.
     *
     * Приклад:
     * min = 9 (09:00), max = 21 (21:00)
     * 1) Повідомлення призначені на 22:47 будуть відправлятися в 9:47 завтрашнього дня.
     * 2) Повідомлення призначені на 07:15 будуть відправлятися в 9:15 утра.
     * 3) Повідомлення призначені на 06:59 будуть відправлятися в 9:59 (майже в 10 годин дня).
     * 4) Повідомлення призначені на 19:59 будуть відправлятися в 19:59, як і вказано.
     */
    'min_hour' => 9,
    'max_hour' => 21,

    /**
     * Точні налаштування HTTP client.
     *
     * http_response_timeout - maximum number of seconds to wait for a response
     * http_retry_max_time - the maximum number of times the request should be attempted
     * http_retry_delay - the number of milliseconds that Laravel should wait in between attempts
     */
    'http_response_timeout' => 3,
    'http_retry_max_time' => 2,
    'http_retry_delay' => 200,

];
