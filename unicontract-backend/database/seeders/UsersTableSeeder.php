<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User as User;
use Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'f.a.pileo@staff.univpm.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'f.a.pileo@staff.univpm.it' ,
                'password' => Hash::make( 'testadm1n' ) ,
                'name' => 'Francesco Antonio Pileo' ,
                'v_ie_ru_personale_id_ab'=> 186177,
                'cf' => 'PLIFNC91T02E885P',
                'nome' => 'Francesco Antonio',
                'cognome' => 'Pileo'
            ] );
        }
        if (!$user->hasRole('super-admin')){
            $user->assignRole('super-admin');
        }

        $user = User::where('email', 'd.attanasio@staff.univpm.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'd.attanasio@staff.univpm.it' ,
                'password' => Hash::make( 'testadm1n' ) ,
                'name' => 'Dante Attanasio' ,
                'v_ie_ru_personale_id_ab'=> 195317,
                'cf' => 'TTNDNT81P22C933C',
                'nome' => 'Dante',
                'cognome' => 'Attanasio'
            ] );
        }
        if (!$user->hasRole('super-admin')){
            $user->assignRole('super-admin');
        }


        $user = User::where('email', 'test.admin@univpm.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'test.admin@univpm.it' ,
                'password' => Hash::make( 'testadm1n' ) ,
                'name' => 'test admin' ,
                'cf' => '12346789LLLLLLL',
                'nome' => 'test',
                'cognome' => 'admin'
                //'v_ie_ru_personale_id_ab'=> 39842,
            ] );
        }
        if (!$user->hasRole('admin')){
            $user->assignRole('admin');
        }

        $user = User::where('email', 'test.user@univpm.it')->first();
        if ($user==null){
            $user = User::firstOrCreate([
                'email' => 'test.user@univpm.it' ,
                'password' => Hash::make( 'testuser' ) ,
                'name' => 'test user' ,
                'cf' => '12346789LLLLLLL',
                'nome' => 'test',
                'cognome' => 'user'
            ] );
        }
        if (!$user->hasRole('viewer')){
            $user->assignRole('viewer');
        }

    }
}
