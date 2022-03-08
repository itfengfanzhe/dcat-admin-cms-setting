<?php

namespace Dcat\Admin\CmsSetting;

use Dcat\Admin\CmsSetting\Models\CmsMakeSetting;
use Dcat\Admin\Extend\Setting as Form;

class Setting extends Form
{
    public function form()
    {
        $this->switch('can_delete', '删除选项卡')->default(0);
        $this->switch('can_add_tab', '新增选项卡')->default(1);
    }
}
