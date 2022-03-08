<?php

namespace Dcat\Admin\CmsSetting\Forms;

use Dcat\Admin\CmsSetting\Models\CmsSetting as Setting;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;


class SettingForm extends Form implements LazyRenderable
{
    use LazyWidget;
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $setting = Setting::query()->first();
        if (!$setting) {
            $setting = new Setting();
        }

        $data = json_decode($setting->data, true);
        foreach ($input as $key => $val) {
            $data[$key] = $val;
        }

        $data = json_encode($data);
        $setting->data = $data;

        if (!$setting->save()) return $this->response()->error('保存失败');

        return $this
				->response()
				->success('操作成功')
				->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text("title", '网站标题');
        $this->text("sub_title", '网站副标题');
        $this->image("logo", '网站logo')->uniqueName();
    }

    /**
     * The data of the form.
     *
     * @return array
     */

    public function default()
    {
        $setting = Setting::query()->first();
        if (!$setting) return [];

        return json_decode($setting->data, true);
    }
}
