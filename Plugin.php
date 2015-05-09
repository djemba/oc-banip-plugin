<?php namespace filipac\Banip;

use Backend\Facades\Backend;
use Cms\Classes\Layout;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use filipac\Banip\Models\Settings;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use System\Classes\PluginBase;

/**
 * Banip Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Ban IP',
            'description' => 'Simple plugin to ban certain IPs',
            'author'      => 'filipac',
            'icon'        => 'icon-leaf',
            'version'     => '1.0.1'
        ];
    }

    public function registerNavigation()
    {
        return [
            'banip' => [
                'label'       => 'Banned IPs',
                'url'         => Backend::url('filipac/banip/ips'),
                'icon'        => 'icon-ban',
                'permissions' => ['filipac.banip.*'],
                'order'       => 500,

                'sideMenu' => [
                    'ips' => [
                        'label'       => 'IP List',
                        'icon'        => 'icon-list',
                        'url'         => Backend::url('filipac/banip/ips'),
                        'permissions' => ['filipac.banip.*'],
                    ],
                    'layout' => [
                        'label'       => 'Settings',
                        'icon'        => 'icon-cog',
                        'url'         => Backend::url('system/settings/update/filipac/banip/settings'),
                        'permissions' => ['filipac.banip.*'],
                    ]
                ]

            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Banned ips behaviour',
                'description' => 'Manage user based settings.',
                'category'    => 'Banned IPS',
                'icon'        => 'icon-cog',
                'class'       => 'filipac\Banip\Models\Settings',
                'order'       => 500
            ]
        ];
    }


    public function register()
    {

    }


    public function boot()
    {
        $ip = static::_checks_ip();
        try {
            $match = \filipac\Banip\Models\Ip::where('address','=',$ip)->get();
        } catch (QueryException $e) {
            \Log::info('The Filipac.Banip was not properly installed (missing table)');
            return;
        }
        if(!$this->isAdmin() && !$this->isCommandLineInterface() && $match->count() >= 1) {
            Event::listen('cms.page.beforeRenderPage', function($cl, $page) {
                $default = 'Your IP has been banned!';
                $content = Settings::get('content',$default);
                if(Str::length($content) == 0) $content = $default;
                return $content;
            });
            Event::listen('cms.page.display', function($controller, $path, $page, $content) {
                $layout = Settings::get('layout');
                if(!empty($layout) AND Layout::load(Theme::getActiveTheme(), $layout) !== null)
                    $page->layout = $layout;
                else
                    Settings::set('layout',null);
                $res = $controller->runPage($page);
                return Response::make($res);
            });
            //abort(403,'Your IP has been banned!');
        }
    }


    public function provides()
    {
        return ['filipac/ip'];
    }


    private function isAdmin(){
        $prefix = ltrim(Config::get('cms.backendUri', 'backend'), '/');
        return Str::startsWith(Request::path(), $prefix);
    }

    private function isCommandLineInterface()
    {
        return (php_sapi_name() === 'cli');
    }

    private static function _checks_ip() {
        if($_SERVER) {
            if( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
                return $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if( isset($_SERVER["HTTP_CLIENT_IP"]) ) {
                return $_SERVER["HTTP_CLIENT_IP"];
            } else if( isset($_SERVER["REMOTE_ADDR"]) ) {
                return $_SERVER["REMOTE_ADDR"];
            } else {
                return 'Error';
            }
        } else {
            if( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
                return getenv( 'HTTP_X_FORWARDED_FOR' );
            } else if( getenv( 'HTTP_CLIENT_IP' ) ) {
                return getenv( 'HTTP_CLIENT_IP' );
            } else if( getenv( 'REMOTE_ADDR' ) ) {
                return getenv( 'REMOTE_ADDR' );
            } else {
                return 'Error';
            }
        }
    }


}
