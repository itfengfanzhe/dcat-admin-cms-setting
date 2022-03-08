<?php

namespace Dcat\Admin\CmsSetting\Forms;

use Dcat\Admin\CmsSetting\Models\CmsMakeSetting;
use Dcat\Admin\CmsSetting\Models\CmsSetting as Setting;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;


class MakeSettingForm extends Form implements LazyRenderable
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
        // 判断这个tabel标签名字是否存在
        $is = CmsMakeSetting::query()->where('tab_name', $input['tab_name'])->exists();
        if ($is) {
            return $this->response()->error('tab名称已存在');
        }
        $model = new CmsMakeSetting();
        $model->tab_name = $input['tab_name'];
        $model->field_json = json_encode($input['form_json']);

        if (!$model->save()) {
            return $this->response()->error('新增失败');
        }

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
        $this->row(function ($row) {
            $row->text("tab_name", 'tab名称')->required();
        });

        $this->row(function ($row) {
            $row->width(24)->table('form_json', '字段', function ($table) {
                $table->text('label', '标题');
                $table->text('field', '字段');
                $table->select('type', '类型')->options(
                    [
                        'text' => '文本',
                        'select' => '下拉',
                        'radio' => '单选',
                        'checkbox' => '多选',
                        'textarea' => '文本域',
                        'image' => '图片上传'
                    ]
                );
                $table->text('model', '模型')->placeholder("example: User.options")->help('模型名称.方法名称，注意大小写');
            });
        });

    }

    /**
     * The data of the form.
     *
     * @return array
     */

    public function default()
    {
        return [];
    }
}
