function submitbutton(value){
	jQuery("#adminForm").attr("action",jQuery("#adminForm").attr("action")+"&task="+value);
	jQuery("#adminForm").submit();
}
wpda_timer_class={
	start_tab_id:"timer_set_time",
	current_tab_id:"timer_set_time",
	after_countdown_end_type:{
		hide:[""],
		text:["after_countdown_text"],
		redirect:["after_countdown_redirect"],
	},
	before_countup_start_type:{
		hide:[""],
		text:["before_countup_text"],
	},
	timer_coundown_type:{
		countdown:["timer_end_date","timer_start_time","timer_coundown_repeat"],
		countup:["timer_end_date","timer_start_time","timer_coundown_repeat"],
		session_countup:["timer_seesion_time","after_countdown_repeat_time"],
		session_countdown:['timer_seesion_time',"after_countdown_repeat_time"],
	},
	timer_coundown_repeat:{
		none:[""],
		when_end:["after_countdown_repeat_time",'repeat_end'],
		daily:['repeat_daily_quantity','repeat_end',"repeat_countdown_start_time","repeat_countdown_end_time"],
		//weekly:['repeat_end',"repeat_countdown_start_time","repeat_countdown_end_time"],
		//monthly:['repeat_end',"repeat_countdown_start_time","repeat_countdown_end_time"],
	},
	repeat_end:{
		never:[""],
		after:["repeat_ending_after"],
		on_date:["repeat_ending_after_date"],
	},
	start:function(){
		var self=this;
		jQuery(document).ready(function(){
			self.conect_tab_activate_functionality();
			self.activete_tab(self.start_tab_id);
			self.initialize_timpickers();
			self.show_hide_elems_on_select_change();
			jQuery("#timer_coundown_type").trigger('change');
			self.pro_feature_click();
		})
	},
	conect_tab_activate_functionality:function(){
		var self=this;
		jQuery(".wpda_timer_link_tabs li").click(function(){
			self.current_tab_id=jQuery(this).attr('id').replace("_tab","");
			self.activete_tab(self.current_tab_id);
			jQuery((".all_options_panel table tr" + "."+self.current_tab_id)+ 'select').each(function(){
				curent_select_id=jQuery(this).attr('id');				
				if(typeof(self.curent_select_id)!="undefined"){
					self.show_hide_elements_by_select_val(self.curent_select_id,jQuery("#"+self.curent_select_id))
				}
			})
		});
	},
	activete_tab:function(tab_id){
		var self = this;
		jQuery(".wpda_timer_link_tabs li,.all_options_panel table tr").removeClass('active');	
		jQuery("#"+tab_id+"_tab").addClass('active');
		jQuery((".all_options_panel table tr" + "."+tab_id)).addClass('active');
		jQuery((".all_options_panel table tr" + "."+tab_id)).find('select').each(function(){
			cur_element_id=jQuery(this).attr('id')
			if(typeof(self[cur_element_id])!="undefined"){
				self.show_hide_elements_by_select_val(self[cur_element_id],cur_element_id)
			}
		})
	},
	initialize_timpickers:function(){
		jQuery('.wpda_datepicker_timer').datetimepicker({
			controlType:'slider',
			dateFormat: "dd/mm/yy",
			TimeFormat: "h:m",
		});
		jQuery('.ui-datepicker-trigger').addClass("button");		
	},
	show_hide_elems_on_select_change:function(){
		var self=this;
		var select_arrays=['after_countdown_end_type',"before_countup_start_type","timer_coundown_type","timer_coundown_repeat","repeat_end"];
		var count_select_arrays=select_arrays.length;		
		for(var i = 0; i < count_select_arrays;i++){
			jQuery("#"+select_arrays[i]).change(function(){
				current_elemtn=jQuery(this).attr('id');
				self.show_hide_elements_by_select_val(self[current_elemtn],current_elemtn)			
			});
		}
	},
	
	show_hide_elements_by_select_val:function(select_info,select_val){		
		var self=this;		
		var all_options=self.make_unic_array_for_select_hide(select_info);
		self.hide_elements_by_array(all_options);
		var active_options=self.make_array_for_select_active(select_val);		
		self.active_elemes_by_array(active_options);
	},
	hide_elements_by_array:function(elems_array){
		if(!Array.isArray(elems_array)){
			return false;
		}		
		var count=elems_array.length;
		for(var i = 0; i < count; i++){			
			jQuery("[name='"+elems_array[i]+"'],[name^='"+elems_array[i]+"[']").eq(0).closest('.tr_option ').removeClass('active');
		}
	},
	active_elemes_by_array:function(elems_array){
		if(!Array.isArray(elems_array)){
			return false;
		}		
		var count=elems_array.length;
		for(var i = 0; i < count; i++){
			jQuery("[name='"+elems_array[i]+"'],[name^='"+elems_array[i]+"[']").eq(0).closest('.tr_option ').addClass('active');
		}
	},
	make_unic_array_for_select_hide:function(obj){
		var self = this;
		var unic_array = new Array();
		jQuery.each(obj,function(key,value){
			var count_loc_array=value.length;
			for(var i=0;i < count_loc_array; i++){
				if(!unic_array.includes(value[i])){
					unic_array.push(value[i]);
					if(typeof(self[value[i]])!="undefined"){
						unic_array=unic_array.concat(self.make_unic_array_for_select_hide(self[value[i]]));						
						if(unic_array.length>10000){
							alert("something go wrong you in programm now in unlimit circle contact to wpdevart");
							return [];
						}
					}
				}
			}
		})		
		return unic_array;
	},
	make_array_for_select_active:function(obj_id){
		var self = this;
		var obj=self[obj_id];
		var obj_val=jQuery("#"+obj_id).val()
		var all_active_element=[];
		var count=0;
		if(typeof(obj[obj_val])!="undefined")
			count=obj[obj_val].length;		
		for(var i=0;i<count; i++){			
			all_active_element.push(obj[obj_val][i]);
			if(typeof(self[obj[obj_val][i]])!="undefined"){				
				all_active_element=all_active_element.concat(self.make_array_for_select_active(obj[obj_val][i]));				
				if(all_active_element.length>10000){
					alert("something go wrong you in programm now in unlimit circle contact to wpdevart");
					return [];
				}
			}
		}		
		return all_active_element;		
	},
	pro_feature_click:function(){
		jQuery('.wpda_countdown_extended_pro_feature input,.wpda_countdown_extended_pro_feature select,.wpda_countdown_extended_pro_feature .wp-picker-container,.wpda_countdown_extended_pro_feature').mousedown(function(){
			jQuery(alert("If you want to use this feature upgrade to Countdown Pro"));
			return false;
		})
	}
	
}

wpda_timer_class.start();