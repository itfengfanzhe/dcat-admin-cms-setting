<?php

namespace Dcat\Admin\CmsSetting\Http\Controllers;

use Dcat\Admin\CmsSetting\CmsSettingServiceProvider;
use Dcat\Admin\CmsSetting\Forms\MakeSettingForm;
use Dcat\Admin\CmsSetting\Forms\OtherSettingForm;
use Dcat\Admin\CmsSetting\Forms\SettingForm;
use Dcat\Admin\CmsSetting\Models\CmsMakeSetting;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Routing\Controller;

class CmsSettingController extends Controller
{
    public function index(Content $content)
    {
        // 查询所有setting
        $setting = CmsMakeSetting::query()->get();
        $tab = new Tab();
        $tab->add('基本信息', SettingForm::make(), true);
        foreach ($setting as $val) {
            $tab->add($val->tab_name, OtherSettingForm::make()->payload(['id' => $val->id]));
        }
        if (CmsSettingServiceProvider::setting('can_add_tab')) {
            $tab->add('新增设置', MakeSettingForm::make());
        }
        return $content
            ->title('设置')
            ->description('所有系统相关设置')
            ->body($tab->withCard());
    }
}
