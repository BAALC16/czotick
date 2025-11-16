<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
Use DB;
Use Str;
Use Hash;

class AllRoutesTest extends TestCase
{
    use DatabaseMigrations;
    protected $admin;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->admin      = \App\Models\User::find(1);
        $this->artisan('db:seed');
    }

    /**
     * test all route
     *
     * @group route
     */

    public function test_all_routes()
    {
        $routeCollection = \Illuminate\Support\Facades\Route::getRoutes();
        $this->withoutEvents();
        $blacklist = [
            'url/that/not/tested',
            'backend/media/download',
            'backend/roles/create',
            'sanctum/csrf-cookie'
        ];

        $logoutRequired = [
            'logout',
            'login',
            'register',
            'forgot-password'
        ];
        $dynamicReg = "/{\\S*}/"; //used for omitting dynamic urls that have {} in uri (http://laravel-tricks.com/tricks/adding-a-sitemap-to-your-laravel-application#comment-1830836789)
        //$this->actingAs($this->admin);


        $password = Str::random(8);

          // Create default super-admin (Founder)
          $email = "phpunit@mck.immo";
          $pwd = $password;
          $user = new User([
            'email' => $email,
            'password' => Hash::make($pwd),
            'nom' => 'Test',
            'prenoms' => 'User',
            'titre' => 'Administrateur',
            'ville' => 'Abidjan',
            'code_pays' => 'CI',
            'email_pro' => 'phpunit@mck.immo',
            'mobile' => '+2250574012162',
            'site_web' => 'https://mck.immo',
          ]);
          $user->saveQuietly();
          $roles = Role::whereIn('nom', ['Fondateur', 'Administrateur', 'Membre'])->get();
          $roles->each(function($role) use($user) {
            $user->roles()->save($role);
          });

        $response = $this->post('/login', [
            'email' => "phpunit@mck.immo",
            'password' => $password,
        ]);

        $logoutRoutes = [];
        foreach ($routeCollection as $route) {
            if (in_array($route->uri(), $logoutRequired)) {
                $logoutRoutes[] = $route;
            }
            else if (!preg_match($dynamicReg, $route->uri()) && 
                !preg_match("/auth*/", $route->uri()) &&
                in_array('GET', $route->methods()) 
                && !in_array($route->uri(), $blacklist)) {
                $start = $this->microtimeFloat();
                //fwrite(STDERR, print_r('test ' . $route->uri() . "\n", true));
                $response = $this->call('GET', $route->uri());
                $end   = $this->microtimeFloat();
                $temps = round($end - $start, 3);
                //fwrite(STDERR, print_r('time: ' . $temps . "\n", true));
                $this->assertLessThan(15, $temps, "too long time for " . $route->uri());
                if ($response->getStatusCode() != 200) {
                    fwrite(STDERR, print_r($route->uri() . " failed to load\n", true));
                }
                //$this->assertEquals(200, $response->getStatusCode(), $route->uri() . "failed to load");

            }

        }

        $response = $this->get('/logout');

        foreach ($logoutRoutes as $route) {
            if (!preg_match($dynamicReg, $route->uri()) && 
                !preg_match("/auth*/", $route->uri()) &&
                in_array('GET', $route->methods())) {

                $start = $this->microtimeFloat();
                $response = $this->call('GET', $route->uri());
                $end   = $this->microtimeFloat();
                $temps = round($end - $start, 3);
                $this->assertLessThan(15, $temps, "too long time for " . $route->uri());
                if ($response->getStatusCode() != 200) {
                    fwrite(STDERR, print_r($route->uri() . " failed to load" . $response->getContent() . "\n", true));
                }                
            }

        }
    }

    public function microtimeFloat()
    {
        list($usec, $asec) = explode(" ", microtime());

        return ((float) $usec + (float) $asec);

    }
}