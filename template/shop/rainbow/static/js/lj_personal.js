$(function(){
	$('#lj_anniu').click(function(){
		if($(this).hasClass("lj_c")){
			$(this).removeClass("lj_c");
		}else{
			$(this).addClass("lj_c");
		}
		if($('#lj_label').hasClass("lj_l")){
			$('#lj_label').removeClass("lj_l");
		}else{
			$('#lj_label').addClass("lj_l");
		}
	})
	$('.lj_t_m_r').click(function(){
		if($(".lj_t_m_r").hide()){
			
			$(".lj_t_m_r2").show();
			$(".lj_t_hid").show();
		}else{
			$(".lj_t_m_r2").hide()
		}
	})
	$('.lj_t_m_r2').click(function(){
		if($(".lj_t_m_r2").hide()){
			$(".lj_t_m_r").show();
			$(".lj_t_hid").hide();
		}else{
			$(".lj_t_m_r").hide()
		}
	})
		$('.lj_t_m_r3').click(function(){
		if($(".lj_t_m_r3").hide()){
			
			$(".lj_t_m_r4").show();
			$(".lj_t_hid1").show();
		}else{
			$(".lj_t_m_r4").hide()
		}
	})
	$('.lj_t_m_r4').click(function(){
		if($(".lj_t_m_r4").hide()){
			$(".lj_t_m_r3").show();
			$(".lj_t_hid1").hide();
		}else{
			$(".lj_t_m_r3").hide()
		}
	})
			$('.lj_t_m_r5').click(function(){
		if($(".lj_t_m_r5").hide()){
			
			$(".lj_t_m_r6").show();
			$(".lj_t_hid2").show();
		}else{
			$(".lj_t_m_r6").hide()
		}
	})
	$('.lj_t_m_r6').click(function(){
		if($(".lj_t_m_r6").hide()){
			$(".lj_t_m_r5").show();
			$(".lj_t_hid2").hide();
		}else{
			$(".lj_t_m_r5").hide()
		}
	})
			$('.lj_t_m_r7').click(function(){
		if($(".lj_t_m_r7").hide()){
			
			$(".lj_t_m_r8").show();
			$(".lj_t_hid3").show();
		}else{
			$(".lj_t_m_r8").hide()
		}
	})
	$('.lj_t_m_r8').click(function(){
		if($(".lj_t_m_r8").hide()){
			$(".lj_t_m_r7").show();
			$(".lj_t_hid3").hide();
		}else{
			$(".lj_t_m_r7").hide()
		}
	})
			$('.lj_t_m_r9').click(function(){
		if($(".lj_t_m_r9").hide()){
			
			$(".lj_t_m_r10").show();
			$(".lj_t_hid4").show();
		}else{
			$(".lj_t_m_r10").hide()
		}
	})
	$('.lj_t_m_r10').click(function(){
		if($(".lj_t_m_r10").hide()){
			$(".lj_t_m_r9").show();
			$(".lj_t_hid4").hide();
		}else{
			$(".lj_t_m_r9").hide()
		}
	})
			$('.lj_t_m_r11').click(function(){
		if($(".lj_t_m_r11").hide()){
			
			$(".lj_t_m_r12").show();
			$(".lj_t_hid5").show();
		}else{
			$(".lj_t_m_r12").hide()
		}
	})
	$('.lj_t_m_r12').click(function(){
		if($(".lj_t_m_r12").hide()){
			$(".lj_t_m_r11").show();
			$(".lj_t_hid5").hide();
		}else{
			$(".lj_t_m_r11").hide()
		}
	})
		$('.lj_t_m_r13').click(function(){
		if($(".lj_t_m_r13").hide()){
			
			$(".lj_t_m_r14").show();
			$(".lj_t_hid6").show();
		}else{
			$(".lj_t_m_r14").hide()
		}
	})
	$('.lj_t_m_r14').click(function(){
		if($(".lj_t_m_r14").hide()){
			$(".lj_t_m_r13").show();
			$(".lj_t_hid6").hide();
		}else{
			$(".lj_t_m_r13").hide()
		}
	})
	/*自定义*/
	$('#zt').click(function(){
		$(".lj_bg").show();
		$(".lj_tank").show();
	})
	
	$('#lj_t_btn,#lj_t_btn2').click(function(){
		$(".lj_bg").hide();
		$(".lj_tank").hide();
	})
	
	$(".lj_edit_p2").each(function(){
		$(this).click(function(){
			$(".lj_edit_p2").css({"border":"1px solid #999","color":"#000"});
			$(this).css({"border":"1px solid #ffbca4","color":"#ffbca4"});
		})
	})
	
})
