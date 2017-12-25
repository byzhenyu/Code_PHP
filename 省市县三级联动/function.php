<?php
/**
 * 通用分页处理函数
 * @param Int $count 总条数
 * @param int $page_size 分页大小
 * @return Array  ['page']分页数据  ['limit']查询调用的limit条件
 */
function get_page($count, $page_size = 0) {
    if ($page_size == 0) $page_size = C('PAGE_SIZE');
    $page = new \Think\AjaxPage($count, $page_size);
    $show = $page->show();
    $limit = $page->firstRow . ',' . $page->listRows;
    return array('page' => $show, 'limit' => $limit);
}
