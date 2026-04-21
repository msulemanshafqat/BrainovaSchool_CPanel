<?php

namespace Database\Seeders\StudentInfo;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentInfo\SessionClassStudent;
use App\Traits\CommonHelperTrait;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    use CommonHelperTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($c = 1; $c <= 3; $c++) { // class
            for ($s = 1; $s <= 2; $s++) { // sections
                for ($i = 1; $i <= 14; $i++) { // students

                    $dob = $faker->dateTimeBetween('-18 years', '-5 years')->format('Y-m-d');
                    $firstName = $faker->firstName;
                    $lastName = $faker->lastName;
                    $fullName = $firstName . ' ' . $lastName;
                    $email = 'student'.$i.$s.$c. '@onest.com';
                    $phone = $faker->unique()->phoneNumber;

                    $avatarPath = createAvatarAndUpload($firstName, $lastName);

                    $user = User::create([
                        'name'              => $fullName,
                        'phone'             => $phone,
                        'email'             => $email,
                        'email_verified_at' => now(),
                        'password'          => Hash::make('123456'),
                        'role_id'           => 6,
                        'date_of_birth'     => $dob,
                        'uuid'              => Str::uuid(),
                        'permissions'       => [],
                        'upload_id' => $avatarPath
                    ]);

                    $student = Student::create([
                        'user_id'                 => $user->id,
                        'admission_no'            => '2023' . $c . $s . $i,
                        'roll_no'                 => $i,
                        'first_name'              => $firstName,
                        'last_name'               => $lastName,
                        'mobile'                  => $phone,
                        'email'                   => $email,
                        'dob'                     => $dob,
                        'admission_date'          => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                        'religion_id'             => rand(1, 3),
                        'department_id'           => rand(1, 3),
                        'blood_group_id'          => rand(1, 8),
                        'gender_id'               => rand(1, 2),
                        'parent_guardian_id'      => rand(1, 10),
                        'student_category_id'     => rand(1, 2),
                        'status'                  => 1,
                        'previous_school_info'    => $faker->company . ', ' . $faker->city,
                        'previous_school'         => 1,
                        'emergency_contact'       => $faker->phoneNumber,
                        'spoken_lang_at_home'     => $faker->randomElement(['English', 'Hindi', 'Arabic', 'Spanish']),
                        'nationality'             => $faker->randomElement(['Bangladeshi', 'Canadian', 'British', 'American']),
                        'place_of_birth'          => $faker->city . ', ' . $faker->country,
                        'residance_address'       => $faker->address,
                        'upload_documents'        => []
                    ]);

                    SessionClassStudent::create([
                        'session_id'  => setting('session'),
                        'student_id'  => $student->id,
                        'classes_id'  => $c,
                        'section_id'  => $s,
                        'shift_id'    => rand(1, 3),
                        'roll'        => $i
                    ]);
                }
            }
        }
    }
}
