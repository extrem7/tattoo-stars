<?php

namespace Database\Factories\User;

use App\Models\Country;
use App\Models\User;
use App\Models\User\Gender;
use App\Models\User\Information;
use Illuminate\Database\Eloquent\Factories\Factory;

class InformationFactory extends Factory
{
    protected $model = Information::class;

    public function definition(): array
    {
        return [
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'birthday' => $this->faker->date(),
            'city_id' => Country::find('UA')->cities()->inRandomOrder()->first()->id,
            'address' => $this->faker->address,
            'bio' => $this->faker->realText(),
            'phone' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'instagram' => $this->faker->word,
            'facebook' => 'https://www.facebook.com/' . $this->faker->word,
        ];
    }
}
