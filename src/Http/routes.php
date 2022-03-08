<?php

use Dcat\Admin\CmsSetting\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('cms-setting', Controllers\CmsSettingController::class.'@index');
