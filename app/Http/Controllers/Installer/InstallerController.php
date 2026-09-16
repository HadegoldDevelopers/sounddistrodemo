<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Services\SitemapGenerator;
use Exception;
use PDO;

class InstallerController extends Controller
{
    private array $steps = [
        1 => 'requirements',
        2 => 'database',
        3 => 'environment',
        4 => 'admin',
        5 => 'finish',
    ];

    // ─────────────────────────────────────────────
    // STEP 0 — Welcome / Landing
    // ─────────────────────────────────────────────
    public function welcome()
    {
        if ($this->isInstalled()) {
            return redirect('/')->with('error', 'Application is already installed.');
        }

        return view('installer.welcome');
    }

    // ─────────────────────────────────────────────
    // STEP 1 — Server Requirements
    // ─────────────────────────────────────────────
    public function requirements()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        $phpVersion = PHP_VERSION;
        $phpOk      = version_compare($phpVersion, '8.1.0', '>=');

        $requirements = [
            ['label' => 'PHP Version >= 8.1',   'status' => $phpOk,                        'value' => $phpVersion],
            ['label' => 'BCMath Extension',      'status' => extension_loaded('bcmath'),    'value' => extension_loaded('bcmath')    ? 'Enabled' : 'Missing'],
            ['label' => 'Ctype Extension',       'status' => extension_loaded('ctype'),     'value' => extension_loaded('ctype')     ? 'Enabled' : 'Missing'],
            ['label' => 'cURL Extension',        'status' => extension_loaded('curl'),      'value' => extension_loaded('curl')      ? 'Enabled' : 'Missing'],
            ['label' => 'DOM Extension',         'status' => extension_loaded('dom'),       'value' => extension_loaded('dom')       ? 'Enabled' : 'Missing'],
            ['label' => 'Fileinfo Extension',    'status' => extension_loaded('fileinfo'),  'value' => extension_loaded('fileinfo')  ? 'Enabled' : 'Missing'],
            ['label' => 'JSON Extension',        'status' => extension_loaded('json'),      'value' => extension_loaded('json')      ? 'Enabled' : 'Missing'],
            ['label' => 'Mbstring Extension',    'status' => extension_loaded('mbstring'),  'value' => extension_loaded('mbstring')  ? 'Enabled' : 'Missing'],
            ['label' => 'OpenSSL Extension',     'status' => extension_loaded('openssl'),   'value' => extension_loaded('openssl')   ? 'Enabled' : 'Missing'],
            ['label' => 'PDO Extension',         'status' => extension_loaded('pdo'),       'value' => extension_loaded('pdo')       ? 'Enabled' : 'Missing'],
            ['label' => 'PDO MySQL Driver',      'status' => extension_loaded('pdo_mysql'), 'value' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Missing'],
            ['label' => 'Tokenizer Extension',   'status' => extension_loaded('tokenizer'), 'value' => extension_loaded('tokenizer') ? 'Enabled' : 'Missing'],
            ['label' => 'XML Extension',         'status' => extension_loaded('xml'),       'value' => extension_loaded('xml')       ? 'Enabled' : 'Missing'],
            ['label' => 'ZipArchive Extension',  'status' => extension_loaded('zip'),       'value' => extension_loaded('zip')       ? 'Enabled' : 'Missing'],
        ];

        $permissions = [
            ['label' => 'storage/app',       'path' => storage_path('app'),            'status' => is_writable(storage_path('app')),            'required' => '775'],
            ['label' => 'storage/framework', 'path' => storage_path('framework'),      'status' => is_writable(storage_path('framework')),      'required' => '775'],
            ['label' => 'storage/logs',      'path' => storage_path('logs'),           'status' => is_writable(storage_path('logs')),           'required' => '775'],
            ['label' => 'bootstrap/cache',   'path' => base_path('bootstrap/cache'),   'status' => is_writable(base_path('bootstrap/cache')),   'required' => '775'],
            [
                'label'    => 'public/temp',
                'path'     => public_path('temp'),
                'status'   => is_writable(public_path()) || (is_dir(public_path('temp')) && is_writable(public_path('temp'))),
                'required' => '775',
            ],
        ];

        $allRequirementsMet = collect($requirements)->every(fn($r) => $r['status']);
        $allPermissionsMet  = collect($permissions)->every(fn($p) => $p['status']);
        $canProceed         = $allRequirementsMet && $allPermissionsMet;

        return view('installer.requirements', compact(
            'requirements',
            'permissions',
            'canProceed',
            'allRequirementsMet',
            'allPermissionsMet'
        ));
    }

    // ─────────────────────────────────────────────
    // STEP 2 — Database Configuration
    // ─────────────────────────────────────────────
    public function database()
    {
        if ($this->isInstalled()) return redirect('/');
        return view('installer.database');
    }

    public function databaseSave(Request $request)
    {
        $request->validate([
            'db_host'     => 'required|string',
            'db_port'     => 'required|numeric',
            'db_name'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $dsn = "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_name}";
            $pdo = new PDO($dsn, $request->db_username, $request->db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            return back()->withErrors([
                'db_connection' => 'Could not connect to database: ' . $e->getMessage()
            ])->withInput();
        }

        session([
            'installer.db_host'     => $request->db_host,
            'installer.db_port'     => $request->db_port,
            'installer.db_name'     => $request->db_name,
            'installer.db_username' => $request->db_username,
            'installer.db_password' => $request->db_password,
        ]);

        return redirect()->route('installer.environment');
    }

    // ─────────────────────────────────────────────
    // STEP 3 — App / Environment Settings
    // ─────────────────────────────────────────────
    public function environment()
    {
        if ($this->isInstalled()) return redirect('/');

        if (!session('installer.db_host')) {
            return redirect()->route('installer.database')
                ->with('error', 'Please complete database setup first.');
        }

        return view('installer.environment');
    }

    public function environmentSave(Request $request)
    {
        $request->validate([
            'site_name'     => 'required|string|max:100',
            'site_url'       => 'required|url',
            'app_env'       => 'required|in:production,local',
            'contact_email' => 'required|email',
            'mail_driver'   => 'required|in:smtp,mailgun,ses,log',
            'mail_host'     => 'nullable|string',
            'mail_port'     => 'nullable|numeric',
            'mail_user'     => 'nullable|string',
            'mail_pass'     => 'nullable|string',
            'mail_from'     => 'nullable|email',
            'mail_name'     => 'nullable|string',
        ]);

        session([
            'installer.site_name'     => $request->site_name,
            'installer.site_url'      => $request->site_url,
            'installer.app_env'       => $request->app_env,
            'installer.contact_email' => $request->contact_email,
            'installer.mail_driver'   => $request->mail_driver,
            'installer.mail_host'     => $request->mail_host,
            'installer.mail_port'     => $request->mail_port,
            'installer.mail_user'     => $request->mail_user,
            'installer.mail_pass'     => $request->mail_pass,
            'installer.mail_from'     => $request->mail_from,
            'installer.mail_name'     => $request->mail_name,
        ]);

        return redirect()->route('installer.license');
    }

    // ─────────────────────────────────────────────
    // STEP 4 — License
    // ─────────────────────────────────────────────
    public function license()
    {
        if ($this->isInstalled()) return redirect('/');

        if (!session('installer.site_name')) {
            return redirect()->route('installer.environment')
                ->with('error', 'Please complete environment setup first.');
        }

        return view('installer.license');
    }

    public function licenseSave(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string|max:100',
        ]);

        $license = app(\App\Services\LicenseService::class);

        if (!$license->verifyOnInstall($request->purchase_code)) {
            $reason = $license->lastReason;

            $message = match ($reason) {
                'domain_limit' => 'This purchase code is already registered to its maximum number of domains.',
                'envato_error' => 'The purchase code could not be verified with Envato right now. Please try again later.',
                'server_unreachable' => 'The license server could not be reached. Please check your connection.',
                default => 'Invalid purchase code. Please check and try again.',
            };

            return back()->withErrors(['purchase_code' => $message]);
        }

        session()->put('installer.purchase_code', $request->purchase_code);

        return redirect()->route('installer.admin');
    }

    // ─────────────────────────────────────────────
    // STEP 5 — Admin Account
    // ─────────────────────────────────────────────
    public function admin()
    {
        if ($this->isInstalled()) return redirect('/');

        if (!session('installer.site_name')) {
            return redirect()->route('installer.environment')
                ->with('error', 'Please complete environment setup first.');
        }

        return view('installer.admin');
    }

    public function adminSave(Request $request)
    {
        $request->validate([
            'admin_name'     => 'required|string|max:100',
            'admin_email'    => 'required|email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // 1. Write .env file
            $this->writeEnvFile($request);

            // 2. Reload database config immediately
            config([
                'database.connections.mysql.host'     => session('installer.db_host'),
                'database.connections.mysql.port'     => session('installer.db_port'),
                'database.connections.mysql.database' => session('installer.db_name'),
                'database.connections.mysql.username' => session('installer.db_username'),
                'database.connections.mysql.password' => session('installer.db_password'),
            ]);

            DB::purge('mysql');
            DB::reconnect('mysql');

            // 3. Run migrations directly
            Artisan::call('migrate', ['--force' => true]);


            // 4. Seed core data directly
            Artisan::call('db:seed', [
                '--class' => 'DatabaseSeeder',
                '--force' => true,
            ]);


            // 5. Save site settings from installer input
            \App\Models\Setting::setValue('site_name',     session('installer.site_name'));
            \App\Models\Setting::setValue('site_url', session('installer.site_url'));
            \App\Models\Setting::setValue('contact_email', session('installer.contact_email'));

            // 6. Persist the verified license
            if ($code = session('installer.purchase_code')) {
                \App\Models\Setting::setValue('license_code', $code);
                \App\Models\Setting::setValue('license_failures', 0);
                \App\Models\Setting::setValue('license_state', 'valid');
                \App\Models\Setting::setValue('license_cache', json_encode(['checked_at' => time(), 'valid' => true]));
            }

            // 7. Create admin account
            \App\Models\Admin::create([
                'name'     => $request->admin_name,
                'email'    => $request->admin_email,
                'password' => Hash::make($request->admin_password),
            ]);

            // 8. Create installed lock file
            File::put(storage_path('installed'), json_encode([
                'installed_at' => now()->toIso8601String(),
                'version'      => config('app.version', '1.0.0'),
            ]));

            SitemapGenerator::generate();

            // 9. Clear opcache if available
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            // 10. Clear installer session
            session()->forget(array_keys(session()->all()));

            return redirect()->route('installer.finish');
        } catch (Exception $e) {
            \Log::error('Installer error: ' . $e->getMessage());
            return back()->withErrors([
                'install' => 'Installation failed: ' . $e->getMessage()
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // STEP 5 — Finish
    // ─────────────────────────────────────────────
    public function finish()
    {
        return view('installer.finish');
    }

    // ─────────────────────────────────────────────
    // Complete — Self destruct and redirect
    // ─────────────────────────────────────────────
    public function complete(Request $request)
    {
        $this->selfDestruct();

        if ($request->get('redirect') === 'home') {
            return redirect('/');
        }

        return redirect(config('app.url') . '/admin/login');
    }

    // ─────────────────────────────────────────────
    // AJAX — Test DB Connection
    // ─────────────────────────────────────────────
    public function testConnection(Request $request)
    {
        try {
            $dsn = "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_name}";
            $pdo = new PDO($dsn, $request->db_username, $request->db_password ?? '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return response()->json(['success' => true, 'message' => 'Connection successful!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    private function selfDestruct(): void
    {
        // Delete installer controller directory
        File::deleteDirectory(app_path('Http/Controllers/Installer'));

        // Delete installer middleware files
        File::delete(app_path('Http/Middleware/InstallerMiddleware.php'));
        File::delete(app_path('Http/Middleware/RedirectIfNotInstalled.php'));

        // Delete ALL installer views including finish
        File::deleteDirectory(resource_path('views/installer'));

        // Delete installer routes file
        File::delete(base_path('routes/installer.php'));

        // Remove installer require from web.php
        $webRoutes = File::get(base_path('routes/web.php'));
        $webRoutes = str_replace("require __DIR__ . '/installer.php';\n", '', $webRoutes);
        File::put(base_path('routes/web.php'), $webRoutes);

        // Remove installer middleware from bootstrap/app.php
        $appFile = File::get(base_path('bootstrap/app.php'));
        $appFile = str_replace(
            "        \$middleware->append(\App\Http\Middleware\RedirectIfNotInstalled::class);\n",
            '',
            $appFile
        );
        $appFile = str_replace(
            "            'installer.check' => \App\Http\Middleware\InstallerMiddleware::class,\n",
            '',
            $appFile
        );
        File::put(base_path('bootstrap/app.php'), $appFile);
    }

    private function isInstalled(): bool
    {
        return File::exists(storage_path('installed'));
    }

    private function writeEnvFile(Request $request): void
    {
        $s      = session();
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        // 1. Detect a missing .env and clone .env.example if needed. This
        //    guarantees a valid base before we overwrite any values.
        if (!File::exists(base_path('.env'))) {
            if (!File::exists(base_path('.env.example'))) {
                throw new Exception('.env.example is missing from the installation.');
            }
            File::copy(base_path('.env.example'), base_path('.env'));
        }

        // 2. Build the installer-specific values to apply.
        $values = [
            'APP_NAME'    => $s->get('installer.site_name'),
            'APP_ENV'     => $s->get('installer.app_env', 'production'),
            'APP_KEY'     => $appKey,
            'APP_DEBUG'   => 'false',
            'APP_URL'     => $s->get('installer.site_url'),

            'LOG_LEVEL'   => 'error',

            'DB_CONNECTION' => 'mysql',
            'DB_HOST'     => $s->get('installer.db_host'),
            'DB_PORT'     => $s->get('installer.db_port', 3306),
            'DB_DATABASE' => $s->get('installer.db_name'),
            'DB_USERNAME' => $s->get('installer.db_username'),
            'DB_PASSWORD' => $s->get('installer.db_password'),

            'MAIL_MAILER'     => $s->get('installer.mail_driver', 'smtp'),
            'MAIL_HOST'       => $s->get('installer.mail_host'),
            'MAIL_PORT'       => $s->get('installer.mail_port', 465),
            'MAIL_USERNAME'   => $s->get('installer.mail_user'),
            'MAIL_PASSWORD'   => $s->get('installer.mail_pass'),
            'MAIL_ENCRYPTION' => 'ssl',
            'MAIL_FROM_ADDRESS' => $s->get('installer.mail_from'),
            'MAIL_FROM_NAME'  => $s->get('installer.site_name'),

            'VITE_APP_NAME' => $s->get('installer.site_name'),
        ];

        // 3. Overwrite the values in-place, preserving any other keys that
        //    already exist in the .env (e.g. ACRCloud, gateway keys).
        $env = File::get(base_path('.env'));

        foreach ($values as $key => $value) {
            $env = $this->writeEnvKey($env, $key, $value);
        }

        File::put(base_path('.env'), $env);
    }

    /**
     * Set or replace a single KEY=VALUE line inside a .env string, preserving
     * the rest of the file. Values with spaces/special chars are quoted.
     */
    private function writeEnvKey(string $env, string $key, ?string $value): string
    {
        $value = (string) $value;

        if ($value === '' || preg_match('/[\s#"\']/', $value)) {
            $line = $key . '="' . str_replace('"', '\\"', $value) . '"';
        } else {
            $line = $key . '=' . $value;
        }

        if (preg_match('/^' . preg_quote($key) . '=.*$/m', $env)) {
            return preg_replace(
                '/^' . preg_quote($key) . '=.*$/m',
                $line,
                $env
            );
        }

        return rtrim($env) . "\n" . $line . "\n";
    }
}
