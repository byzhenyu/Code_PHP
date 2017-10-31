<?php if (!defined('THINK_PATH')) exit();?><ul id='sub_nav'>
	<?php if(is_array($subNav)): foreach($subNav as $key=>$v): ?><li><a class="current-<?php echo ($v["id"]); ?>" href="<?php echo U($v['url'], array('current_id'=>$v['id'], 'main_nav_id'=>$parent_id));?>" ><span><?php echo ($v["title"]); ?></span></a></li><?php endforeach; endif; ?>
</ul>

<script>
	$(function(){
		var current_id = '<?php echo ($current_id); ?>';
		if (current_id <= 0) {
			// 默认点击菜单的第一个
            $('#sub_nav').find('a').eq(0).addClass('sub-nav-current');
		} else {
			$('.current-' + current_id).addClass('sub-nav-current');
		}
		
	})
</script>