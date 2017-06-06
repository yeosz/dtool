<?php

$title = $this->database . '数据库设计';
$tables = $this->getTable();
$foreignKey = $this->getForeignKey();
$primary = $this->getPrimaryKey();
$triggers = $this->getTrigger();
$index = $this->getIndex();
$defaultComment = $this->defaultComment;

$html = '';
// 循环所有表
foreach ($tables as $key => $table) {
    $html .= "\n";
    $html .= '<table>';
    // 字段
    $html .= '<thead>';
    $html .= '<tr><th colspan="8">' . ($key + 1) . '&nbsp;' . $table['table_name'] . '&nbsp;' . $table['table_comment'] . '</th><th>' . $table['engine'] . '</th></tr>';
    $html .= '<tr>';
    $html .= '<td>序号</td>';
    $html .= '<td>字段名</td>';
    $html .= '<td>数据类型</td>';
    $html .= '<td>默认值</td>';
    $html .= '<td>允许非空</td>';
    $html .= '<td>自动递增</td>';
    $html .= '<td>是否主键</td>';
    $html .= '<td>外键关系</td>';
    $html .= '<td>备注</td>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';
    foreach ($table['column'] as $order => $column) {
        $primaryStr = in_array($column['table_schema'] . '.' . $column['table_name'] . '.' . $column['column_name'], $primary) ? '是' : '';
        $columnForeignKeyStr = isset($foreignKey[$column['table_name'] . '.' . $column['column_name']]) ? $foreignKey[$column['table_name'] . '.' . $column['column_name']] : '';
        if ($column['column_comment'] == '' && isset($defaultComment[$column['column_name']]))
        {
            $column['column_comment'] = $defaultComment[$column['column_name']];
        }
        $html .= '<tr>';
        $html .= '<td class="w50 text-center">' . ($order + 1) . '</td>';
        $html .= '<td class="w120">' . $column['column_name'] . '</td>';
        $html .= '<td class="w120">' . $column['column_type'] . '</td>';
        $html .= '<td class="w80 text-center">' . $column['column_default'] . '</td>';
        $html .= '<td class="w80 text-center">' . $column['is_nullable'] . '</td>';
        $html .= '<td class="w80 text-center">' . ($column['extra'] == 'auto_increment' ? '是' : '&nbsp;') . '</td>';
        $html .= '<td class="w80 text-center">' . $primaryStr . '</td>';
        $html .= '<td class="w300">' . $columnForeignKeyStr . '</td>';
        $html .= '<td class="w300">' . $column['column_comment'] . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody>';

    // 触发器
    if (isset($triggers[$table['table_name']])) {
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<td colspan="2">触发器名称</td>';
        $html .= '<td>触发</td>';
        $html .= '<td>类型</td>';
        $html .= '<td colspan="5">定义</td>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';
        foreach ($triggers[$table['table_name']] as $trigger) {
            $html .= '<tr>';
            $html .= '<td colspan="2" class="w120">' . $trigger['name'] . '</td>';
            $html .= '<td class="w120 text-center">' . $trigger['timing'] . '</td>';
            $html .= '<td class="w80 text-center">' . $trigger['event'] . '</td>';
            $html .= '<td colspan="5">' . $trigger['statement'] . '</td>';
            $html .= '</tr>';
            $html .= '</tbody>';
        }
    }
    // 索引
    if (isset($index[$table['table_name']])) {
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<td colspan="2">索引名称</td>';
        $html .= '<td>唯一索引</td>';
        $html .= '<td>索引类型</td>';
        $html .= '<td colspan="5">字段</td>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';
        foreach ($index[$table['table_name']] as $item) {
            $html .= '<tr>';
            $html .= '<td colspan="2" class="w120">' . $item['index_name'] . '</td>';
            $html .= '<td class="w80 text-center">' . ($item['non_unique'] ? '否' : '是') . '</td>';
            $html .= '<td class="w120 text-center">' . $item['index_type'] . '</td>';
            $html .= '<td colspan="5">' . $item['column_name'] . '</td>';
            $html .= '</tr>';
            $html .= '</tbody>';
        }
    }

    $html .= '</table>' . "\n";
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title?></title>
    <style>
    body, td, th { font-family: "微软雅黑"; font-size: 14px; }
    .warp{margin:auto; width:80%;}
    .warp h3{margin:0; padding:0; line-height:30px; margin-top:10px;}
    table { border-collapse: collapse; border: 1px solid #000; background: #efefef; margin-bottom:20px; }
    table thead th { background-color:#d3d3d3;text-align: left; font-weight: bold; height: 30px; line-height: 30px; font-size: 16px; border: 1px solid #000; padding:5px;}
    table thead td { background-color:#d3d3d3;text-align: left; font-weight: bold; height: 26px; line-height: 26px; font-size: 14px; text-align:center; border: 1px solid #000; padding:5px; color:grey;}
    table td { height: 20px; font-size: 14px; border: 1px solid #000; background-color: #fff; padding:5px;}
    .w120 { width: 120px; }
    .w80 { width: 80px; }
    .w50 { width: 50px; }
    .w300 { width: 300px; }
    .text-center{text-align:center;}
    </style>
</head>
<body>
    <div class="warp">
        <h1 style="text-align:center;"><?php echo $title?></h1>
        <?php echo $html; ?>
    </div>
</body>
</html>