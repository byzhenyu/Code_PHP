$('form').submit(function(){
	var gouxuan = $('.vote-optopns').find('input').is(':checked'); 
	if (!gouxuan) {
		toastr.error('请选择投票选项!');
		return false;
	};
})

$('.vote-result li').each(function(){
	var selfCount = $(this).find('.bar').attr('self-count');
	var allCount = $(this).find('.bar').attr('all-count');
	var barId = 360*(selfCount/allCount);
	$(this).find('.bar').find('.bar-long').addClass('bg-color');
	$(this).find('.bar').find('.bar-long').animate({
       width: '+='+barId+'px'
    }, 800);
});