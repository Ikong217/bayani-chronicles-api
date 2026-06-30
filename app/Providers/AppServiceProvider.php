<?php
namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Blade::directive('nav', function ($title) {
            return <<<PHP
<?php
    \$__title = $title;
    if (!empty(\$__title)) {
        \$__title = " | " . \$__title;
    }
?>
<div class="header">
    <h2>Bayani Chronicles<?php echo \$__title; ?></h2>
    <form method="POST" action="<?php echo route('logout'); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="logout-btn dropdown-item">
            LOGOUT
        </button>
    </form>
</div>
PHP;
        });

    }
}
