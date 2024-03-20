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

    'accepted'             => ':attribute debe ser aceptado.',
    'active_url'           => ':attribute no es una URL válida.',
    'after'                => ':attribute debe ser posterior a :date.',
    'after_or_equal'       => ':attribute debe ser posterior o igual a :date.',
    'alpha'                => ':attribute sólo debe tener letras.',
    'alpha_dash'           => ':attribute sólo puede contener letras, números y puntos.',
    'alpha_num'            => ':attribute sólo puede contener letras y números.',
    'array'                => ':attribute debe ser de an array.',
    'before'               => ':attribute debe ser anterior a :date.',
    'before_or_equal'      => ':attribute debe ser anterior o igual a :date.',
    'between'              => [
        'numeric' => ':attribute debe estar entre :min y :max.',
        'file'    => ':attribute debe estar entre :min y :max kilobytes.',
        'string'  => ':attribute debe estar entre :min y :max caracteres.',
        'array'   => ':attribute must have between :min y :max items.',
    ],
    'boolean'              => ':attribute debe ser un valor verdadero o falso.',
    'confirmed'            => ':attribute y la confirmación no coinciden.',
    'date'                 => ':attribute no es una fecha valida.',
    'date_format'          => ':attribute debe tener el siguiente formato :format.',
    'different'            => ':attribute y :other deben ser disitntos.',
    'digits'               => ':attribute debe ser de :digits digitos.',
    'digits_between'       => ':attribute debe estar entre :min y :max digitos.',
    'dimensions'           => ':attribute tiene un tamaño inválido.',
    'distinct'             => 'Ya existe un :attribute con ese valor.',
    'email'                => ':attribute debe ser una dirección de correo electrónico válida. Debe contener @ y ..',
    'exists'               => ':attribute es inválido.',
    'file'                 => ':attribute debe ser un archivo.',
    'filled'               => ':attribute debe tener valor.',
    'image'                => ':attribute debe ser una imagen.',
    'in'                   => ':attribute es inválido.',
    'in_array'             => ':attribute no existe en :other.',
    'integer'              => ':attribute debe ser un número entero.',
    'ip'                   => ':attribute debe ser una dirección IP válida.',
    'json'                 => ':attribute debe ser un JSON válido.',
    'max'                  => [
        'numeric' => 'attribute no puede ser mayor que :max.',
        'file'    => ':attribute no puede ser mayor que :max kilobytes.',
        'string'  => ':attribute no puede tener más de :max caracteres.',
        'array'   => ':attribute no puede tener más de :max items.',
    ],
    'mimes'                => ':attribute debe ser de los siguientes extensiones: :values.',
    'mimetypes'            => ':attribute debe ser de los siguientes extensiones: :values.',
    'min'                  => [
        'numeric' => ':attribute debe ser de al menos :min.',
        'file'    => ':attribute debe ser de al menos :min kilobytes.',
        'string'  => ':attribute debe ser de al menos :min caracteres.',
        'array'   => ':attribute debe tener al menos :min items.',
    ],
    'not_in'               => 'El dato :attribute es inválido.',
    'numeric'              => ':attribute debe ser un número.',
    'present'              => ':attribute debe existir.',
    'regex'                => 'El formato  de ::attribute es inválido.',
    'required'             => 'Debe completar el dato :attribute.',
    'required_if'          => 'Debe completar el dato :attribute cuando :other es :value.',
    'required_unless'      => 'Debe completar el dato :attribute a menos que :other sea :values.',
    'required_with'        => 'Debe completar el dato :attribute si completo :values.',
    'required_with_all'    => 'Debe completar el dato :attribute si completo :values.',
    'required_without'     => 'Debe completar el dato :attribute si no completo :values.',
    'required_without_all' => 'Debe completar el dato :attribute si no completo los siguientes datos :values.',
    'same'                 => ':attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => ':attribute debe ser de :size.',
        'file'    => ':attribute debe ser de :size kilobytes.',
        'string'  => ':attribute debe ser de :size caracteres.',
        'array'   => ':attribute must contain :size items.',
    ],
    'string'               => ':attribute debe ser una cadena de texto.',
    'timezone'             => ':attribute debe ser una zona horaria válida.',
    'unique'               => 'Ya existe un :attribute con ese valor.',
    'uploaded'             => 'El :attribute no pudo ser cargado.',
    'url'                  => 'El formato de :attribute es inválido.',

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
        'mayuscula'            => 'Debe empezar con mayúscula',
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
      'adjunto'                 => 'Adjuntar Archivo',
      'apellido'                => 'Apellido',
      'color'                   => 'Color',
      'cuit'                    => 'CUIT/CUIL',
      'description'             => 'Descripción',
      'descargar'               => 'Descargar CV',
      'descripcion'             => 'Descripción',
      'display_name'            => 'Nombre a mostrar',
      'email'                   => 'Correo Electrónico',
      'name'                    => 'Nombre',
      'nombre'                  => 'Nombre',
      'numero'                  => 'Número',
      'observaciones'           => 'Observaciones',
      'password'                => 'Contraseña',
      'title'                   => 'Título',
      'titulo'                  => 'Título',
      'user'                    => 'Usuario',
    ],

];
