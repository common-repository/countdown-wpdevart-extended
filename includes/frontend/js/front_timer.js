/*standart countdown protytype_script*/
(function($){	
	// standart countdown 
	$.fn.wpdevart_countdown_extend_standart = function(options,calback) {
		var element = $(this);
		// curent seconds left
		var seconds_left=options.seconds_left;
		var timer_countup_seconds=-options.timer_start_time;		
		var array_of_dates=['week','day','hour','minut','second'];
		var interval_ids = new Array();
		/* calculating Date */
		var kaificents_by_seconds={
				week:604800,
				day:86400,
				hour:3600,
				minut:60,
				second:1,
		}
		var loc_kaificents=get_kaificents();		
		var kaificents=loc_kaificents[0];
		var count_of_display_dates=loc_kaificents[1];
		var timer_exsist=false;
		delete loc_kaificents;
		/*end of Calculating Dates*/		
		create_html();
		// if repeating exist then create the set time for it will work
		if(options.repeat_points.length>0){
			for(var i=0;i<options.repeat_points.length;i++){
				repeat_settimeout(i);
			}
		}		
		if(options.coundown_type=="countup" || options.coundown_type=="session_countup"){
			if(options.timer_start_time>0){
				before_timer();
				seconds_left=seconds_left-options.timer_start_time;
				timer_countup_seconds=0;
				setTimeout(function() {start_counting_up();},options.timer_start_time*1000+10);
			}else{
				setTimeout(function() {start_counting_up();},10);
			}
			
		}else{
			if(options.timer_start_time>0){
				before_timer();	
				seconds_left=seconds_left-options.timer_start_time;				
				setTimeout(function() {start_counting_down();},options.timer_start_time*1000+10)
			}else{
				setTimeout(function() {start_counting_down();},10)
			}
		}	
		// create html 
		function create_html(){
			if(options.top_html_text!=''){
				element.append('<div class="wpdevart_top_html">'+options.top_html_text+'</div>')
			}
			for(i=0;i<array_of_dates.length;i++){
				if(typeof(options.display_days[array_of_dates[i]])=="undefined"){
					continue;
				}else{
					element.append('<div class="wpdevart_countdown_extend_element '+array_of_dates[i]+'_block_element"><span class="time_left_extended '+array_of_dates[i]+'_left"></span><span class="time_text '+array_of_dates[i]+'_text"></span></div>')
				}
			}
			if(options.bottom_html_text!=''){
				element.append('<div class="wpdevart_bottom_html">'+options.bottom_html_text+'</div>')
			}
			set_html_text(options.display_days);
			/*Set animation effect*/
			timer_exsist=true;			
		}
		// remove html element
		function remove_html(){
			element.html("");
			timer_exsist=false;
		}
		
		//  start counting down
		function start_counting_down(){			
			remove_html();create_html();
			time_object=(calculate_time(seconds_left));
			change_html_time(time_object);
			interval_ids.push(setInterval(function(){if(seconds_left<=0){after_timer();}seconds_left--;time_object=(calculate_time(seconds_left));change_html_time(time_object);},1000));		
			if( seconds_left <= 0 ){
				after_timer();
			}
		}
		
		//start counting up 
		function start_counting_up(){	
			if(timer_countup_seconds>=0 && seconds_left>0){
				if(!timer_exsist){
					remove_html();
					create_html();
				}
				time_object=(calculate_time(timer_countup_seconds));				
				change_html_time(time_object);								
				interval_ids.push(setInterval(function(){if(seconds_left<=0){after_timer();}seconds_left--;timer_countup_seconds++;time_object=(calculate_time(timer_countup_seconds));change_html_time(time_object);},1000));
			}else{
				after_timer();
			}
		}
		function repeat_settimeout(index){			
			setTimeout(function() {				
				if(index==0){
					seconds_left=options.repeat_seconds_start;
					timer_countup_seconds=options.repeat_seconds_mid-options.repeat_seconds_start;
				}else if(index==options.repeat_points.length-1){
					seconds_left=options.repeat_seconds_mid;
					timer_countup_seconds=0;
				}else{
					seconds_left=options.repeat_seconds_end;
					timer_countup_seconds=options.repeat_seconds_mid-options.repeat_seconds_end;
				}
				if(!timer_exsist){
					if(options.coundown_type=="countup" || options.coundown_type=="session_countup"){
						start_counting_up();
					}else{
						start_counting_down();
					}
				}
			}, (options.repeat_points[index])*1000);
		}
		// set text for html
		function set_html_text(text_of_html){			
			jQuery.each(text_of_html,function(index,value){
				element.find('.'+index+'_text').html(options.display_days_texts[index]);
			})
		}
		// change time
		function change_html_time(time_object){	
			jQuery.each(time_object,function(index,value){
				element.find('.'+index+'_left').html(value);
			})
		}
		/* get day kaificents*/
		function get_kaificents(){
			var kaificent={
				week:1000,
				day:7,
				hour:24,
				minut:60,
				second:60,
			}
			var k=5;
			if(typeof(options.display_days.week)=="undefined"){
				kaificent["day"]=kaificent["week"]*7;
				delete kaificent.week;
				k--;
			}
			if(typeof(options.display_days.day)=="undefined"){
				kaificent["hour"]=kaificent["day"]*24;
				delete kaificent.day;
				k--;
			}
			if(typeof(options.display_days.hour)=="undefined"){
				kaificent["minut"]=kaificent["hour"]*60;
				delete kaificent.hour;
				k--;
			}
			if(typeof(options.display_days.minut)=="undefined"){
				kaificent["second"]=kaificent["minut"]*60;
				delete kaificent.minut;
				k--;
			}
			if(typeof(options.display_days.second)=="undefined"){
				delete kaificent.second;
				k--;
			}
			return[kaificent,k];
		}
		/*Caluclating time*/
		function calculate_time(seconds){	
			var time_object={};
			var loc_seconds_left=seconds;
			var k=0;
			jQuery.each(kaificents,function(index,value){
				k++;
				if(k==count_of_display_dates && loc_seconds_left!=0){
					time_object[index]=Math.min(Math.ceil(loc_seconds_left/kaificents_by_seconds[index]),value);
					loc_seconds_left=loc_seconds_left-time_object[index]*kaificents_by_seconds[index];
					
				}else{
					time_object[index]=Math.min(Math.floor(loc_seconds_left/kaificents_by_seconds[index]),value);
					loc_seconds_left=loc_seconds_left-time_object[index]*kaificents_by_seconds[index];
				}
			})
			return time_object;
		}
		
		/*after Countdown and functions*/
		function after_timer(){
			switch(options.after_countdown_end_type){
				case "hide":
					hide_countdown();
					break;
				case "text":
					show_countdown_text();
					break;
				case "repeat":						
					hide_countdown();
					break;
				case "redirect":
					redirect_countdown();
					break;
				default:
					hide_countdown();
					break;
			}
		}
		function before_timer(){
			switch(options.before_countup_start_type){
				case "none":
					hide_countdown();
					break;
				case "text":
					show_countdown_before_text();
					break;
			}
		}
		function hide_countdown(){
			clear_intervals();
			element.remove();
		}
		function show_countdown_text(){
			clear_intervals();
			remove_html();
			element.html(options.after_countdown_text);
		}
		function repeat_countdown(){
			seconds_left=options.repeat_seconds;			
			if(options.coundown_type=="countup" || options.coundown_type=="session_countup"){
				clear_intervals();
				timer_countup_seconds=0;
				start_counting_up();
			}
		}
		function redirect_countdown(){
			clear_intervals();
			if(equals_url(window.location.href,options.after_countdown_redirect) || options.after_countdown_redirect=="" ||  options.after_countdown_redirect==window.location.href ){
				hide_countdown();
			}else{
				window.location=options.after_countdown_redirect
			}
		}
		function equals_url(url1,url2) {		
			// remove any prefix
			url2 = url2.replace("http://", "");
			url2 = url2.replace("http://www.", "");
			url2 = url2.replace("https://", "");
			url2 = url2.replace("https://www.", "");
			
			url1 = url1.replace("http://", "");
			url1 = url1.replace("http://www.", "");
			url1 = url1.replace("https://", "");
			url1 = url1.replace("https://www.", "");
			// assume any URL that starts with a / is on the current page's domain
			if (url1.substr(url1.length - 1) != "/") {
				url1 = url1+"/"
			}
			if (url2.substr(url2.length - 1) != "/") {
				url2 = url2+"/"
			}
			if(url1===url2){
				return true;
			}
			return false;
		}
		function show_countdown_before_text(){
			timer_exsist=false;
			element.html(options.before_countup_text);
		}
		function clear_intervals(){
			var i=interval_ids.length;
			while(i>0){
				i--
				clearInterval(interval_ids[i]);
				interval_ids.pop();				
			}
		}
		if(parseInt(options.inline)){
			jQuery(document).ready(function(){
				initial_start_parametrs();
				display_line();
			})		
			jQuery(window).resize(function(){display_line()})
		}
		
		function display_line(){
			var main_width=element.parent().width();
			if(main_width==0){
				main_width=element.parent().parent().width();
			}
			var sumary_inside_width=0;
			element.find(".wpdevart_countdown_extend_element").each(function(){
				sumary_inside_width=sumary_inside_width+parseInt(jQuery(this).attr('date-width'))+parseInt(jQuery(this).attr('date-distance'));
			})
			kaificent=(sumary_inside_width)/main_width;
			if(kaificent>=1 && main_width>0){
				element.find(".wpdevart_countdown_extend_element").each(function(){
					jQuery(this).width(Math.floor(parseInt(jQuery(this).attr('date-width'))/kaificent));
					jQuery(this).css("min-width",Math.floor(parseInt(jQuery(this).attr('date-width'))/kaificent));
					jQuery(this).css("margin-right",Math.floor(parseInt(jQuery(this).attr('date-distance'))/kaificent));
					var time_left_extended=jQuery(this).find(".time_left_extended");
					var time_text=jQuery(this).find(".time_text");
					var time_left_extended_param={};
					var time_text_param={};
					time_left_extended_param["font"]=parseInt(time_left_extended.attr("date-font"))/kaificent;
					time_text_param["font"]=parseInt(time_text.attr("date-font"))/kaificent;
					
					time_left_extended_param["margin"]=time_left_extended.attr("date-margin").split(" ");					
					jQuery.each(time_left_extended_param["margin"],function(index,value){
						time_left_extended_param["margin"][index]=Math.floor(parseInt(value)/kaificent);
					})
					time_left_extended_param["margin"]=time_left_extended_param["margin"].join('px ');
					
					time_text_param["margin"]=time_text.attr("date-margin").split(" ");
					jQuery.each(time_text_param["margin"],function(index,value){
						time_text_param["margin"][index]=Math.floor(parseInt(value)/kaificent);
					})
					time_text_param["margin"]=time_text_param["margin"].join('px ');
					
					time_left_extended_param["padding"]=time_left_extended.attr("date-padding").split(" ");
					jQuery.each(time_left_extended_param["padding"],function(index,value){
						time_left_extended_param["padding"][index]=Math.floor(parseInt(value)/kaificent);
					})
					time_left_extended_param["padding"]=time_left_extended_param["padding"].join('px ');
					
					time_text_param["padding"]=time_text.attr("date-padding").split(" ");
					jQuery.each(time_text_param["padding"],function(index,value){
						time_text_param["padding"][index]=Math.floor(parseInt(value)/kaificent);
					})
					time_text_param["padding"]=time_text_param["padding"].join('px ');
					time_left_extended.css("font-size",time_left_extended_param["font"]);
					time_text.css("font-size",time_text_param["font"]);
					time_left_extended.css("margin",time_left_extended_param["margin"]);
					time_text.css("margin",time_text_param["margin"]);
					time_left_extended.css("padding",time_left_extended_param["padding"]);
					time_text.css("padding",time_text_param["padding"]);
					delete(time_left_extended);
					delete(time_text);
					delete(time_left_extended_param);
					delete(time_text_param);
				})	
			}
		}
		function initial_start_parametrs(){
			element.find(".wpdevart_countdown_extend_element").each(function(){
				jQuery(this).attr("date-width",jQuery(this).width());
				jQuery(this).attr("date-distance",jQuery(this).css("margin-right"));
				var time_left_extended=jQuery(this).find(".time_left_extended");
				var time_text=jQuery(this).find(".time_text");
				time_left_extended.attr("date-font",time_left_extended.css("font-size"));
				time_text.attr("date-font",time_text.css("font-size"));
				time_left_extended.attr("date-margin",time_left_extended.css("margin"));
				time_text.attr("date-margin",time_text.css("margin"));
				time_left_extended.attr("date-padding",time_left_extended.css("padding"));
				time_text.attr("date-padding",time_text.css("padding"));
				delete(time_left_extended);
				delete(time_text);
			})
		}
		/*Countup*/
		
		function show_before_text(){
			
		}
	}
})(jQuery)
	