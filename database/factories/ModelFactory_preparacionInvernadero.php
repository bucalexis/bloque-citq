<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\preparacionInvernadero::class, function (Faker\Generator $faker) {

    $invernadero=  DB::table('invernadero')->lists('id');

    return [
        'fecha' => $faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'),
        'tipoSiembra'=> $faker->randomElement(['Charolas','Bolis nuevos','Bolis reciclados','Macetas']),
        'id_invernadero' => $faker->randomElement($invernadero)
    ];
});
