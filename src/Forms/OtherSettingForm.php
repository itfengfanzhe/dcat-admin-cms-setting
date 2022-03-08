<?php

namespace Dcat\Admin\CmsSetting\Forms;

use Dcat\Admin\CmsSetting\CmsSettingServiceProvider;
use Dcat\Admin\CmsSetting\Models\CmsMakeSetting;
use Dcat\Admin\CmsSetting\Models\CmsSetting;
use Dcat\Admin\CmsSetting\Models\CmsSetting as Setting;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Form\Tools;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class OtherSettingForm extends Form implements LazyRenderable
{
    use LazyWidget;
    protected $table_name = '';


    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        if (CmsSettingServiceProvider::setting('can_delete')) {
            if (!empty($input['delete']) && $input['delete'][0] == 1) {
                $setting_id = $input['setting_id'];unset($input['setting_id']);
                CmsMakeSetting::query()->find($setting_id)->delete();
                return $this->response()->success('删除成功')->refresh();
            }
        }
        $form_json = $input['form_json'];unset($input['form_json']);
        $tab_name = $input['tab_name'];unset($input['tab_name']);
        $setting_id = $input['setting_id'];unset($input['setting_id']);
        $make_setting = CmsMakeSetting::query()->find($setting_id);
        $make_setting->tab_name = $tab_name;
        $make_setting->field_json = json_encode($form_json);
        $make_setting->save();

        $setting = Setting::query()->first();
        if (!$setting) {
            $setting = new Setting();
        }

        $data = json_decode($setting->data, true);
        unset($data[$tab_name]);
        foreach ($input as $key => $val) {
            $data[$tab_name][$key] = $val;
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

        // 这个if里边的东西是处理，图片文件等上传失败的问题
        if (isset($this->payload['_current_']) && $this->payload['_current_'] == env('APP_URL').'/admin/dcat-api/form/upload?') {
            $make_settign = CmsMakeSetting::query()->get();
            foreach ($make_settign as $key => $val) {
                $field_json = json_decode($val->field_json, true);
                foreach ($field_json as $fv) {
                    if ($fv['type'] == 'image') {
                        $this->image($fv['field'])->uniqueName();
                    }
                }
            }
        }
        $setting = CmsMakeSetting::query()->find($this->payload['id'] ?? 0);
        $json = json_decode($setting?->field_json ?? '', true) ?? [];
        $this->tab('设置', function () use($json) {
            $this->hidden('is_setting')->value(1);
            foreach ($json as $val) {
                $type = $val['type'];
                $field = $val['field'];
                $label = $val['label'];
                $options = [];
                if (!empty($val['model'])) {
                    $model = explode('.', $val['model'])[0];
                    $action = explode('.', $val['model'])[1] ?? '';
                    $model = "App\\Models\\$model";
                    if (class_exists($model)) {
                        if (method_exists($model, $action)) {
                            $options = (new $model)?->$action() ?? [];
                        } else {
                            $this->html($model.'数据模型中的'.$action.'方法不存在，请检查配置');
                        }
                    } else {
                        $this->html($model.'数据模型不存在');
                    }
                }
                if ($type == 'text') {
                    $this->text($field, $label."($field)");
                } else if ($type == 'radio') {
                    $this->radio($field, $label."($field)")->options($options);
                } elseif ($type == 'checkbox') {
                    $this->checkbox($field, $label."($field)")->options($options);
                } else if ($type == 'select') {
                    $this->select($field, $label."($field)")->options($options);
                } else if ($type == 'textarea') {
                    $this->textarea($field, $label."($field)");
                } else if ($type == 'image') {
                    $this->image($field, $label."($field)")->uniqueName();
                }
            }
        });
        $this->tab('修改', function () use($json, $setting) {
            if (CmsSettingServiceProvider::setting('can_delete')) {
                $this->checkbox('delete', '删除')->options([1 => '删除'])->help('删除整个选项卡配置，删除后不可恢复，谨慎勾选')->default(0);
            }
            $this->hidden('setting_id')->value($setting?->id ?? 0);
            $this->text("tab_name", 'tab名称')->default($setting?->tab_name ?? '')->required();
            $this->table('form_json', '字段', function ($table) {
                $table->text('label', '标题');
                $table->text('field', '字段');
                $table->select('type', '类型')->options(
                    ['text' => '文本', 'select' => '下拉', 'radio' => '单选', 'checkbox' => '多选', 'textarea' => '文本域', 'image' => '图片上传']
                );
                $table->text('model', '模型')->placeholder("example: user,options")->help('模型名称，方法名称');

            })->default($setting?->field_json);
        });

    }


    /**
     * 获取数据库中的所有表
     * @return array
     * @author 张建伟 <itfengfanzhe@163.com>
     */
    protected function getTables()
    {
        // 获取数据库中的所有表
        $tables = DB::select("show tables");
        $tables = array_column($tables, 'Tables_in_'.env("DB_DATABASE"));
        $select_table = [];
        foreach ($tables as $table) {
            $select_table[$table] = $table;
        }
        return $select_table;
    }

    /**
     * The data of the form.
     *
     * @return array
     */

    public function default()
    {
        $make_setting = CmsMakeSetting::query()->find($this->payload['id'] ?? 0);
        $setting = Setting::query()->first();
        if (!$setting) return [];
        $data = json_decode($setting->data, true);

        return $data[$make_setting->tab_name] ?? [];
    }
}
