<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Das Feld :attribute muss akzeptiert werden.',
    'accepted_if' => 'Das Feld :attribute muss akzeptiert werden, wenn :other :value ist.',
    'active_url' => 'Das Feld :attribute muss eine gültige URL sein.',
    'after' => 'Das Feld :attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => 'Das Feld :attribute muss ein Datum nach oder gleich :date sein.',
    'alpha' => 'Das Feld :attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => 'Das Feld :attribute darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
    'alpha_num' => 'Das Feld :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => 'Das Feld :attribute muss ein Array sein.',
    'ascii' => 'Das Feld :attribute darf nur einbyte-alphanumerische Zeichen und Symbole enthalten.',
    'before' => 'Das Feld :attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => 'Das Feld :attribute muss ein Datum vor oder gleich :date sein.',
    'between' => [
        'array' => 'Das Feld :attribute muss zwischen :min und :max Elemente haben.',
        'file' => 'Das Feld :attribute muss zwischen :min und :max Kilobyte groß sein.',
        'numeric' => 'Das Feld :attribute muss zwischen :min und :max liegen.',
        'string' => 'Das Feld :attribute muss zwischen :min und :max Zeichen enthalten.',
    ],
    'boolean' => 'Das Feld :attribute muss entweder wahr oder falsch sein.',
    'can' => 'Das Feld :attribute enthält einen nicht autorisierten Wert.',
    'confirmed' => 'Die Bestätigung für :attribute stimmt nicht überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => 'Das Feld :attribute muss ein gültiges Datum sein.',
    'date_equals' => 'Das Feld :attribute muss ein Datum gleich :date sein.',
    'date_format' => 'Das Feld :attribute muss dem Format :format entsprechen.',
    'decimal' => 'Das Feld :attribute muss :decimal Dezimalstellen haben.',
    'declined' => 'Das Feld :attribute muss abgelehnt werden.',
    'declined_if' => 'Das Feld :attribute muss abgelehnt werden, wenn :other :value ist.',
    'different' => 'Das Feld :attribute und :other müssen unterschiedlich sein.',
    'digits' => 'Das Feld :attribute muss :digits Stellen haben.',
    'digits_between' => 'Das Feld :attribute muss zwischen :min und :max Stellen haben.',
    'dimensions' => 'Das Feld :attribute hat ungültige Bildabmessungen.',
    'distinct' => 'Das Feld :attribute hat einen doppelten Wert.',
    'doesnt_end_with' => 'Das Feld :attribute darf nicht mit einem der folgenden Werte enden: :values.',
    'doesnt_start_with' => 'Das Feld :attribute darf nicht mit einem der folgenden Werte beginnen: :values.',
    'email' => 'Das Feld :attribute muss eine gültige E-Mail-Adresse sein.',
    'ends_with' => 'Das Feld :attribute muss mit einem der folgenden Werte enden: :values.',
    'enum' => 'Das ausgewählte :attribute ist ungültig.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'extensions' => 'Das Feld :attribute muss eine der folgenden Erweiterungen haben: :values.',
    'file' => 'Das Feld :attribute muss eine Datei sein.',
    'filled' => 'Das Feld :attribute muss einen Wert haben.',
    'gt' => [
        'array' => 'Das Feld :attribute muss mehr als :value Elemente haben.',
        'file' => 'Das Feld :attribute muss größer als :value Kilobyte sein.',
        'numeric' => 'Das Feld :attribute muss größer als :value sein.',
        'string' => 'Das Feld :attribute muss mehr als :value Zeichen enthalten.',
    ],
    'gte' => [
        'array' => 'Das Feld :attribute muss mindestens :value Elemente haben.',
        'file' => 'Das Feld :attribute muss größer oder gleich :value Kilobyte sein.',
        'numeric' => 'Das Feld :attribute muss größer oder gleich :value sein.',
        'string' => 'Das Feld :attribute muss mindestens :value Zeichen enthalten.',
    ],
    'hex_color' => 'Das Feld :attribute muss eine gültige hexadezimale Farbe sein.',
    'image' => 'Das Feld :attribute muss ein Bild sein.',
    'in' => 'Das ausgewählte :attribute ist ungültig.',
    'in_array' => 'Das Feld :attribute muss in :other existieren.',
    'integer' => 'Das Feld :attribute muss eine ganze Zahl sein.',
    'ip' => 'Das Feld :attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => 'Das Feld :attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => 'Das Feld :attribute muss eine gültige IPv6-Adresse sein.',
    'json' => 'Das Feld :attribute muss eine gültige JSON-Zeichenkette sein.',
    'lowercase' => 'Das Feld :attribute muss klein geschrieben sein.',
    'lt' => [
        'array' => 'Das Feld :attribute muss weniger als :value Elemente haben.',
        'file' => 'Das Feld :attribute muss kleiner als :value Kilobyte sein.',
        'numeric' => 'Das Feld :attribute muss kleiner als :value sein.',
        'string' => 'Das Feld :attribute muss weniger als :value Zeichen enthalten.',
    ],
    'lte' => [
        'array' => 'Das Feld :attribute darf nicht mehr als :value Elemente haben.',
        'file' => 'Das Feld :attribute muss kleiner oder gleich :value Kilobyte sein.',
        'numeric' => 'Das Feld :attribute muss kleiner oder gleich :value sein.',
        'string' => 'Das Feld :attribute darf nicht mehr als :value Zeichen enthalten.',
    ],
    'mac_address' => 'Das Feld :attribute muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => 'Das Feld :attribute darf nicht mehr als :max Elemente haben.',
        'file' => 'Das Feld :attribute darf nicht größer als :max Kilobyte sein.',
        'numeric' => 'Das Feld :attribute darf nicht größer als :max sein.',
        'string' => 'Das Feld :attribute darf nicht mehr als :max Zeichen enthalten.',
    ],
    'max_digits' => 'Das Feld :attribute darf nicht mehr als :max Stellen haben.',
    'mimes' => 'Das Feld :attribute muss eine Datei vom Typ: :values sein.',
    'mimetypes' => 'Das Feld :attribute muss eine Datei vom Typ: :values sein.',
    'min' => [
        'array' => 'Das Feld :attribute muss mindestens :min Elemente haben.',
        'file' => 'Das Feld :attribute muss mindestens :min Kilobyte groß sein.',
        'numeric' => 'Das Feld :attribute muss mindestens :min sein.',
        'string' => 'Das Feld :attribute muss mindestens :min Zeichen enthalten.',
    ],
    'min_digits' => 'Das Feld :attribute muss mindestens :min Stellen haben.',
    'missing' => 'Das Feld :attribute muss fehlen.',
    'not_in' => 'Das ausgewählte :attribute ist ungültig.',
    'not_regex' => 'Das Format des Feldes :attribute ist ungültig.',
    'numeric' => 'Das Feld :attribute muss eine Zahl sein.',
    'required' => 'Das Feld :attribute ist erforderlich.',
    'same' => 'Das Feld :attribute muss mit :other übereinstimmen.',
    'size' => [
        'array' => 'Das Feld :attribute muss genau :size Elemente enthalten.',
        'file' => 'Das Feld :attribute muss :size Kilobyte groß sein.',
        'numeric' => 'Das Feld :attribute muss :size sein.',
        'string' => 'Das Feld :attribute muss genau :size Zeichen enthalten.',
    ],
    'string' => 'Das Feld :attribute muss eine Zeichenkette sein.',
    'timezone' => 'Das Feld :attribute muss eine gültige Zeitzone sein.',
    'unique' => 'Das :attribute wurde bereits vergeben.',
    'uploaded' => 'Das Hochladen von :attribute ist fehlgeschlagen.',
    'url' => 'Das Feld :attribute muss eine gültige URL sein.',
    'uuid' => 'Das Feld :attribute muss eine gültige UUID sein.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
