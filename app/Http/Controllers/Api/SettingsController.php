<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Artisan;

class SettingsController extends Controller{
    
    public function postSettings(Request $request){
        if(!file_exists(config_path().'/intecmex.php')):
            fopen(config_path().'/intecmex.php', 'w');
        endif;

        $file =  fopen(config_path().'/intecmex.php', 'w');

        fwrite($file, '<?php'.PHP_EOL);
        fwrite($file, 'return ['.PHP_EOL);
        foreach ($request->except('_token') as $key => $value):
            if(is_null($value)):
                fwrite($file, '\''.$key.'\' => \'\','.PHP_EOL);
            else:
                fwrite($file, '\''.$key.'\' => \''.$value.'\','.PHP_EOL);
            endif;
        endforeach;
        fwrite($file, ']'.PHP_EOL);
        fwrite($file, '?>'.PHP_EOL);
        fclose($file);

        return redirect()->route('platform_settings_clear');
    }

    public function getSettingsClear(Request $request){
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        return redirect('http://'.$request->getHost().'/settings')->with('message', 'Las configuraciones fueron guardadas con éxito.')->with('typealert', 'success');
    }

}
