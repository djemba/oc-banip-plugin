<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 07-May-15
 * Time: 5:56 PM
 */

namespace Filipac\Banip\Models;

use Cms\Classes\Layout;
use Cms\Classes\Theme;
use Illuminate\Support\Facades\Lang;
use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'filipac_banip';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public function getLayoutOptions() {
        $theme = Theme::getActiveTheme();
        $layouts = Layout::listInTheme($theme, true);
        $result = [];
        $result[null] = Lang::get('cms::lang.page.no_layout');
        foreach ($layouts as $layout) {
            $baseName = $layout->getBaseFileName();
            $result[$baseName] = strlen($layout->name) ? $layout->name : $baseName;
        }

        return $result;
    }
}