<?php
namespace Dcat\Admin\CmsSetting\Models;

use Dcat\Admin\Admin;
use Illuminate\Database\Eloquent\Model;

class CmsSetting extends Model
{
    protected $table = 'itffz_cms_setting';

    /**
     * 获取一个值
     * @param string $key
     * @param string $tab
     * @return mixed
     * @author 张建伟 <itfengfanzhe@163.com>
     */
    public static function getValue(string $key, string $tab = ""): mixed
    {
        $data = self::getAllData();
        if ($tab) {
            $data = $data[$tab] ?? [];
            return $data[$key] ?? '';
        }

        return $data[$key] ?? '';
    }

    /**
     * 获取一个标签下所有的配置
     * @param string $tab
     * @return mixed        不存在的标签返回一个空数组
     * @author 张建伟 <itfengfanzhe@163.com>
     */
    public static function getTabSetting(string $tab = ''): mixed
    {
        $data = self::getAllData();
        return $data[$tab] ?? [];
    }

    /**
     * 获取所有配置
     * @return mixed
     * @throws \Exception
     * @author 张建伟 <itfengfanzhe@163.com>
     */
    public static function getAllData(): mixed
    {
        if (!Admin::extension()->enabled('itfengfanzhe.dcat-admin-cms-setting')) {
            throw new \Exception('该扩展未启用');
        }
        $setting = CmsSetting::query()->first();
        return json_decode($setting->data, true);
    }
}
