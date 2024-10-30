<?php
/* Clases For wordpress countdown*/

class wpdevart_countdown_forntend_main{
	protected static $id_counter=0; // for seting counter
	protected $timer; // timer information
	protected $theme; // design information
	protected $mk_time;
	protected $r_p_sec;
	
	// apply settings to 
	function __construct($timer,$theme){
		$this->timer=$timer;
		$this->theme=$theme;
		$this->caluclate_time();
		self::$id_counter++;
	}
	protected function caluclate_time(){		
		$curent_timezone=date_default_timezone_get();
		date_default_timezone_set($this->timer['timer_timezone']);
		$this->mk_time['now']=mktime ((int)date("H"),(int)date("i"),(int)date("s") ,(int)date("n"), (int)date("j"),(int)date("Y"));
		// get start of date		
		$exploded_date_time=explode(" ",$this->timer['timer_start_time']);
		$exploded_date=explode("/",$exploded_date_time[0]);
		$exploded_time=explode(":",$exploded_date_time[1]);
		$year=(int)$exploded_date[2];
		$month=(int)$exploded_date[1];
		$day=(int)$exploded_date[0];		
		$hour=(int)$exploded_time[0];
		$minute=(int)$exploded_time[1];		
		$this->mk_time['start_date']=mktime ($hour, $minute, 0, $month, $day, $year);
		if(!isset($this->timer['version']) && isset($this->timer['timer_coundown_type']) && $this->timer['timer_coundown_type']=="countdown"){
			$this->mk_time['start_date']=mktime ($hour, $minute, 0, $month, $day-1, $year);
		}
		// get end date
		$this->timer['timer_end_date'];
		$exploded_date_time=explode(" ",$this->timer['timer_end_date']);
		$exploded_date=explode("/",$exploded_date_time[0]);
		$exploded_time=explode(":",$exploded_date_time[1]);
		$year=(int)$exploded_date[2];
		$month=(int)$exploded_date[1];
		$day=(int)$exploded_date[0];		
		$hour=(int)$exploded_time[0];
		$minute=(int)$exploded_time[1];
		$this->mk_time['end_date']=mktime ($hour, $minute, 0, $month, $day, $year);
		date_default_timezone_set($curent_timezone);		
	}
	protected function correct_timer_to_personal(){
		if($this->timer['timer_coundown_type']=="session_countdown" || $this->timer['timer_coundown_type']=="session_countup"){
			if(!isset($_SESSION["wpda_countdown_extended_start"])){
				$_SESSION["wpda_countdown_extended_start"]=$this->mk_time['now'];
			}
			$this->mk_time['start_date']=$_SESSION["wpda_countdown_extended_start"];
			$this->mk_time['end_date']=$this->mk_time['start_date']+$this->p_s_l();
			$this->timer['timer_coundown_repeat']="when_end";
			$this->timer['repeat_end']="never";
			if(($this->timer['after_countdown_repeat_time']['hour']*3600+$this->timer['after_countdown_repeat_time']['minute']*60)===0){
				$this->timer['timer_coundown_repeat']="none";
			}
			
		}
		
	}
	protected function is_ended(){
		if($this->mk_time['end_date'] < $this->mk_time['now'])
			return true;
		return false;
	}
	
	protected function is_started(){
		if($this->mk_time['start_date'] >= $this->mk_time['now'])
			return true;
		return false;
	}
	
	//s_l="seconds left" functions
	private function s_l_when_end(){
		$rep_time=$this->timer['after_countdown_repeat_time']['hour']*3600+$this->timer['after_countdown_repeat_time']['minute']*60;
		 if($rep_time>0)
			 return $rep_time;
		 return 300;
	}
	//	p_s_l= personal seconds left
	private function p_s_l(){
		$session_time=$this->timer['timer_seesion_time']['hour']*3600+$this->timer['timer_seesion_time']['minute']*60;
		 if($session_time>0)
			 return $session_time;
		 return 300;
	}
	private function s_l_daily(){
		$start_time=explode(":",$this->timer['repeat_countdown_start_time']);
		$start_time=$start_time[0]*3600+$start_time[1]*60;
		$end_time=explode(":",$this->timer['repeat_countdown_end_time']);
		$end_time=$end_time[0]*3600+$end_time[1]*60;
		return max($end_time-$start_time,10);
	}
	private function s_l_weekly(){
		$start_time=explode(":",$this->timer['repeat_countdown_start_time']);
		$start_time=$start_time[0]*3600+$start_time[1]*60;
		$end_time=explode(":",$this->timer['repeat_countdown_end_time']);
		$end_time=$end_time[0]*3600+$end_time[1]*60;
		return max($end_time-$start_time,10);
	}
	private function s_l_monthly(){
		$start_time=explode(":",$this->timer['repeat_countdown_start_time']);
		$start_time=$start_time[0]*3600+$start_time[1]*60;
		$end_time=explode(":",$this->timer['repeat_countdown_end_time']);
		$end_time=$end_time[0]*3600+$end_time[1]*60;
		return max($end_time-$start_time,10);
	}
	
	
	
	//g_r_b_p = get repeat begin points
	protected function g_r_b_p(){
		$g_r_b_p_array=[];
		switch($this->timer['timer_coundown_repeat']){
			case "when_end":
				$g_r_b_p_array=$this->g_r_b_p_when_end();
			break;
			case "daily":
				$g_r_b_p_array=$this->g_r_b_p_daily();
			break;
			case "weekly":
				$g_r_b_p_array=$this->g_r_b_p_weekly();
			break;
			case "monthly":
				$g_r_b_p_array=$this->g_r_b_p_monthly();
			break;			
		}
		return $g_r_b_p_array;
	}
	
	private function g_r_b_p_when_end(){
		$r_p=[];//repeat point
		switch($this->timer['repeat_end']){
			case "never":
				$r_s_l_when_end=(int)$this->s_l_when_end();//repeat second		
				if(!$this->is_ended()){
					// repeat seconds
					$this->r_p_sec['beg']=$r_s_l_when_end;
					$this->r_p_sec['mid']=$r_s_l_when_end;
					$this->r_p_sec['end']=$r_s_l_when_end;
					
					$repeat_time_interval=$this->mk_time['now']+1728000-$this->mk_time['end_date'];
					if($repeat_time_interval<=0)
						return $r_p;
					$repeat_count=$repeat_time_interval/$r_s_l_when_end;
					if($repeat_count>365) $repeat_count=365;
					for($i=0;$i<$repeat_count;$i++){
						$r_p[$i]=$this->mk_time['end_date']+$i*$r_s_l_when_end-$this->mk_time['now'];
					}
					return $r_p;
				}				
				$this->r_p_sec['mid']=$r_s_l_when_end;
				$this->r_p_sec['end']=$r_s_l_when_end;				
				$repeat_time_interval=1728000;
				$repeat_count=$repeat_time_interval/$r_s_l_when_end;
				$offset_from_start=$r_s_l_when_end-($this->mk_time['now']-$this->mk_time['end_date'])%$r_s_l_when_end;
				$this->r_p_sec['beg']=$offset_from_start;
				if($repeat_count>365) $repeat_count=365;
				$r_p[0]=0;
				for($i=1;$i<$repeat_count;$i++){
					$r_p[$i]=$this->mk_time['now']+($i-1)*$r_s_l_when_end+$offset_from_start-$this->mk_time['now'];
				}
				return $r_p;				
			break;	
			
			case "after":			
				$r_s_l_when_end=(int)$this->s_l_when_end();//repeat second	
					$this->r_p_sec['beg']=$r_s_l_when_end;
					$this->r_p_sec['mid']=$r_s_l_when_end;
					$this->r_p_sec['end']=$r_s_l_when_end;				
				if(!$this->is_ended()){
					$repeat_time_interval=$this->mk_time['now']+1728000-$this->mk_time['end_date'];
					
					if($repeat_time_interval<=0)
						return $r_p;
					$repeat_time_interval=min($this->timer['repeat_ending_after']*$r_s_l_when_end,$repeat_time_interval);
					$repeat_count=$repeat_time_interval/$r_s_l_when_end;
					if($repeat_count>365) $repeat_count=365;
					for($i=0;$i<$repeat_count;$i++){
						$r_p[$i]=$this->mk_time['end_date']+$i*$r_s_l_when_end-$this->mk_time['now'];
					}
					return $r_p;
				}				
				$this->r_p_sec['mid']=$r_s_l_when_end;
				$this->r_p_sec['end']=$r_s_l_when_end;
				$repeat_time_interval=min(1728000,$this->mk_time['end_date']-$this->mk_time['now']+$this->timer['repeat_ending_after']*$r_s_l_when_end);
				if($repeat_time_interval<=0)
						return $r_p;
				$repeat_count=$repeat_time_interval/$r_s_l_when_end;
				$offset_from_start=$r_s_l_when_end-($this->mk_time['now']-$this->mk_time['end_date'])%$r_s_l_when_end;
				$this->r_p_sec['beg']=$offset_from_start;
				if($repeat_count>365) $repeat_count=365;
				$r_p[0]=0;
				for($i=1;$i<$repeat_count;$i++){
					$r_p[$i]=$this->mk_time['now']+($i-1)*$r_s_l_when_end+$offset_from_start-$this->mk_time['now'];
				}
				return $r_p;
			
			break;
			case "on_date":
				$r_s_l_when_end=(int)$this->s_l_when_end();//repeat second			
				if(!$this->is_ended()){
					$this->r_p_sec['beg']=$r_s_l_when_end;
					$this->r_p_sec['mid']=$r_s_l_when_end;
					$this->r_p_sec['end']=$r_s_l_when_end;
					$repeat_time_interval=$this->mk_time['now']+1728000-$this->mk_time['end_date'];					
					$repeat_time_interval=min($this->mk_time['repeat_end_on_date']-$this->mk_time['now'],$repeat_time_interval);
					if($repeat_time_interval<=0)
						return $r_p;
					
					$repeat_count=$repeat_time_interval/$r_s_l_when_end;
					if($repeat_count>365) $repeat_count=365;
					for($i=0;$i<$repeat_count;$i++){
						$r_p[$i]=$this->mk_time['end_date']+$i*$r_s_l_when_end-$this->mk_time['now'];
					}
					$this->r_p_sec['end']=min($this->mk_time['repeat_end_on_date']-$r_p[$repeat_count-1],$r_s_l_when_end);
					return $r_p;
				}
				
				$this->r_p_sec['beg']=$r_s_l_when_end;
				$this->r_p_sec['mid']=$r_s_l_when_end;
				$this->r_p_sec['end']=$r_s_l_when_end;
				$repeat_time_interval=min(1728000,$this->mk_time['repeat_end_on_date']-$this->mk_time['now']);
				if($repeat_time_interval<=0)
						return $r_p;
				$repeat_count=$repeat_time_interval/$r_s_l_when_end;
				$offset_from_start=$r_s_l_when_end-($this->mk_time['now']-$this->mk_time['end_date'])%$r_s_l_when_end;
				$this->r_p_sec['beg']=$offset_from_start;
				if($repeat_count>365) $repeat_count=365;
				$r_p[0]=0;
				for($i=1;$i<$repeat_count;$i++){
					$r_p[$i]=$this->mk_time['now']+($i-1)*$r_s_l_when_end+$offset_from_start-$this->mk_time['now'];
				}
				$this->r_p_sec['end']=min($this->mk_time['repeat_end_on_date']-$r_p[$repeat_count-1],$r_s_l_when_end);
				return $r_p;			
			break;
		}
	}
	
	
	private function g_r_b_p_daily(){
		$r_p=[];//repeat point
		switch($this->timer['repeat_end']){
			case "never":
				$s_l_daily=(int)$this->s_l_daily();
				$this->r_p_sec['beg']=$s_l_daily;
				$this->r_p_sec['mid']=$s_l_daily;
				$this->r_p_sec['end']=$s_l_daily;
				$repeat_in_cur_day=1;		
				$all_count_rep_points=floor(($this->mk_time['now']+1728000-$this->mk_time['begin_of_daily_rep'])/(86400*$this->timer['repeat_daily_quantity']));
				if($all_count_rep_points<0)
					return $r_p;
				$cur_count_pos=floor(($this->mk_time['now']-$this->mk_time['begin_of_daily_rep'])/(86400*$this->timer['repeat_daily_quantity']));
				if($cur_count_pos>=0){						
					if(($this->mk_time['begin_of_daily_rep']+$cur_count_pos*(86400*$this->timer['repeat_daily_quantity'])+$s_l_daily)>=$this->mk_time['now']){						
						$r_p[0]=0;
						$this->r_p_sec['beg']= $s_l_daily-($this->mk_time['now']-$this->mk_time['begin_of_daily_rep']-$cur_count_pos*(86400*$this->timer['repeat_daily_quantity']));
						if($this->r_p_sec['beg']<0)
							$this->r_p_sec['beg']=$s_l_daily;
						$count=$all_count_rep_points-$cur_count_pos;
						for($i=1;$i<=$count;$i++){
							$r_p[$i]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+$i)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
						}
						$this->r_p_sec['end']=min(abs($s_l_daily-($this->mk_time['now']+1728000-($this->mk_time['begin_of_daily_rep']+$cur_count_pos*$count*(86400*$this->timer['repeat_daily_quantity'])))),$s_l_daily);
					}else{
						
						$r_p[0]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+1)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
						$count=$all_count_rep_points-$cur_count_pos;
						for($i=1;$i<$count;$i++){
							$r_p[$i]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+$i)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];						}
						$this->r_p_sec['end']=min(abs($s_l_daily-($this->mk_time['now']+1728000-($this->mk_time['begin_of_daily_rep']+($cur_count_pos+$count)*(86400*$this->timer['repeat_daily_quantity'])))),$s_l_daily);
					}
				}else{
					for($i=0;$i<$all_count_rep_points;$i++){
						$r_p[$i]=$this->mk_time['begin_of_daily_rep']+$i*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
					}
				}
				return $r_p;	
			break;	
			
			case "after":			
				$s_l_daily=(int)$this->s_l_daily();
				$this->r_p_sec['beg']=$s_l_daily;
				$this->r_p_sec['mid']=$s_l_daily;
				$this->r_p_sec['end']=$s_l_daily;
				$repeat_in_cur_day=1;		
				$all_count_rep_points=floor(($this->mk_time['now']+1728000-$this->mk_time['begin_of_daily_rep'])/(86400*$this->timer['repeat_daily_quantity']));
				if($all_count_rep_points<0)
					return $r_p;
				if($all_count_rep_points>$this->timer['repeat_ending_after']){
					$all_count_rep_points=$this->timer['repeat_ending_after'];
				}
				$cur_count_pos=floor(($this->mk_time['now']-$this->mk_time['begin_of_daily_rep'])/(86400*$this->timer['repeat_daily_quantity']));
				if($cur_count_pos>=$this->timer['repeat_ending_after'])
					return $r_p;
				if($cur_count_pos>=0){						
					if(($this->mk_time['begin_of_daily_rep']+$cur_count_pos*(86400*$this->timer['repeat_daily_quantity'])+$s_l_daily)>=$this->mk_time['now']){						
						$r_p[0]=0;
						$this->r_p_sec['beg']= $s_l_daily-($this->mk_time['now']-$this->mk_time['begin_of_daily_rep']-$cur_count_pos*(86400*$this->timer['repeat_daily_quantity']));
						$count=$all_count_rep_points-$cur_count_pos;
						for($i=1;$i<=$count;$i++){
							$r_p[$i]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+$i)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
						}
						$this->r_p_sec['end']=min(abs($s_l_daily-($this->mk_time['now']+1728000-($this->mk_time['begin_of_daily_rep']+$cur_count_pos*$count*(86400*$this->timer['repeat_daily_quantity'])))),$s_l_daily);
					}else{
						
						$r_p[0]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+1)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
						$count=$all_count_rep_points-$cur_count_pos;
						for($i=1;$i<$count;$i++){
							$r_p[$i]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+$i)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];						}
						$this->r_p_sec['end']=min(abs($s_l_daily-($this->mk_time['now']+1728000-($this->mk_time['begin_of_daily_rep']+($cur_count_pos+$count)*(86400*$this->timer['repeat_daily_quantity'])))),$s_l_daily);
					}
				}else{
					for($i=0;$i<$all_count_rep_points;$i++){
						$r_p[$i]=$this->mk_time['begin_of_daily_rep']+$i*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
					}
				}
				return $r_p;
			break;
			case "on_date":
				$s_l_daily=(int)$this->s_l_daily();
				$this->r_p_sec['beg']=$s_l_daily;
				$this->r_p_sec['mid']=$s_l_daily;
				$this->r_p_sec['end']=$s_l_daily;
				$repeat_in_cur_day=1;		
				$all_count_rep_points=floor(($this->mk_time['now']+1728000-$this->mk_time['begin_of_daily_rep'])/(86400*$this->timer['repeat_daily_quantity']));
				$all_count_rep_points=min($all_count_rep_points,1+floor(($this->mk_time['repeat_end_on_date']-$this->mk_time['begin_of_daily_rep'])/(86400*$this->timer['repeat_daily_quantity'])));
				if($all_count_rep_points<0)
					return $r_p;
				$cur_count_pos=floor(($this->mk_time['now']-$this->mk_time['begin_of_daily_rep'])/(86400*$this->timer['repeat_daily_quantity']));
				if(($this->mk_time['repeat_end_on_date']-$this->mk_time['begin_of_daily_rep'] - $cur_count_pos*(86400*$this->timer['repeat_daily_quantity']))<=0)
					return $r_p;
				if($cur_count_pos>=0){						
					if(($this->mk_time['begin_of_daily_rep']+$cur_count_pos*(86400*$this->timer['repeat_daily_quantity'])+$s_l_daily)>=$this->mk_time['now']){						
						$r_p[0]=0;
						$this->r_p_sec['beg']= $s_l_daily-($this->mk_time['now']-$this->mk_time['begin_of_daily_rep']-$cur_count_pos*(86400*$this->timer['repeat_daily_quantity']));
						$this->r_p_sec['beg'] = min($this->r_p_sec['beg'],max(0,$this->mk_time['repeat_end_on_date']-$this->mk_time['now']));						
						$count=$all_count_rep_points-$cur_count_pos;
						for($i=1;$i<=$count;$i++){
							$r_p[$i]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+$i)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
						}
						$end_date=min($this->mk_time['now']+1728000,$this->mk_time['repeat_end_on_date']);
						$this->r_p_sec['end']=min(abs($s_l_daily-($end_date-($this->mk_time['begin_of_daily_rep']+$cur_count_pos*
						$count*(86400*$this->timer['repeat_daily_quantity'])))),$s_l_daily);
							
						
						
					}else{
						
						$r_p[0]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+1)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
						$count=$all_count_rep_points-$cur_count_pos;
						for($i=1;$i<$count;$i++){
							$r_p[$i]=$this->mk_time['begin_of_daily_rep']+($cur_count_pos+$i)*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];						}
						$this->r_p_sec['end']=min(abs($s_l_daily-($this->mk_time['now']+1728000-($this->mk_time['begin_of_daily_rep']+($cur_count_pos+$count)*(86400*$this->timer['repeat_daily_quantity'])))),$s_l_daily);
					}
				}else{
					for($i=0;$i<$all_count_rep_points;$i++){
						$r_p[$i]=$this->mk_time['begin_of_daily_rep']+$i*(86400*$this->timer['repeat_daily_quantity'])-$this->mk_time['now'];
					}
				}
				return $r_p;			
			break;
		}
	}
		
	
	
	protected function get_texts(){
		if($this->theme["countdown_text_type"]=="standart"){
			return array(
				"week"=>$this->theme["text_for_weeks"],
				"day"=>$this->theme["text_for_day"],
				"hour"=>$this->theme["text_for_hour"],
				"minut"=>$this->theme["text_for_minute"],
				"second"=>$this->theme["text_for_second"],
			);
		}else{
			return array(
				"week"=>__("Weeks","wpdevart_countdown_n"),
				"day"=>__("Days","wpdevart_countdown_n"),
				"hour"=>__("Hours","wpdevart_countdown_n"),
				"minut"=>__("Minutes","wpdevart_countdown_n"),
				"second"=>__("Seconds","wpdevart_countdown_n"),
			);
		}
	}
	private function get_session_fixed_time(){
		$curent_timezone=date_default_timezone_get();
			if(!isset($_SESSION["wpdevart_curent_user_session_time".$this->timer["timer_id"]])){
				$_SESSION["wpdevart_curent_user_session_time".$this->timer["timer_id"]]=mktime ((int)date("H"),(int)date("i"),(int)date("s") ,(int)date("n"), (int)date("j"),(int)date("Y"));
			}			
		date_default_timezone_set($curent_timezone);
		return $_SESSION["wpdevart_curent_user_session_time".$this->timer["timer_id"]];
	}
}

// standart_countdown class
class wpdevart_countdown_forntend_stanadart_view extends wpdevart_countdown_forntend_main{

	public function create_countdown(){	
		$this->timer['time_is_expired']="0";
		$params_array=array();	
		
		$params_array["seconds_left"]=max(0,$this->mk_time['end_date']-$this->mk_time['now']);
		$params_array["repeat_points"]=array();
		$params_array["repeat_seconds_start"]='10';
		$params_array["repeat_seconds_mid"]='10';
		$params_array["repeat_seconds_end"]='10';
		$params_array["timer_start_time"]=$this->mk_time['start_date']-$this->mk_time['now'];
		$params_array["time_is_expired"]=$this->timer["time_is_expired"];
		$params_array["after_countdown_end_type"]=$this->timer["after_countdown_end_type"];
		$params_array["after_countdown_text"]=$this->timer["after_countdown_text"];
		$params_array["before_countup_start_type"]=$this->timer["before_countup_start_type"];
		$params_array["before_countup_text"]=$this->timer["before_countup_text"];
		$params_array["coundown_type"]=$this->timer["timer_coundown_type"];	
		$params_array["after_countdown_redirect"]=$this->timer["after_countdown_redirect"];
		$params_array["display_days"]=$this->theme["countdown_date_display"];
		$params_array["gorup_animation"]=$this->theme["countdown_standart_gorup_animation"];
		$params_array["inline"]=$this->theme["countdown_standart_display_inline"];	
		$params_array["top_html_text"]=isset($this->timer["top_countdown_show_html"])?$this->timer["top_countdown_show_html"]:'';
		$params_array["bottom_html_text"]=isset($this->timer["bottom_countdown_show_html"])?$this->timer["bottom_countdown_show_html"]:'';	
		$params_array["effect"]=($this->theme["countdown_standart_animation_type"]=="random")?wpda_contdown_extend_library::get_randowm_animation():$this->theme["countdown_standart_animation_type"];
		$params_array["display_days_texts"]=$this->get_texts();
		$params_converted_to_js_objec=json_encode($params_array);
		$countdown_html='<div class="wpdevart_countdown_extend_standart" id="wpdevart_countdown_'.self::$id_counter.'"></div>';
		$countdown_script='<script>jQuery("#wpdevart_countdown_'.self::$id_counter.'").wpdevart_countdown_extend_standart('.$params_converted_to_js_objec.')</script>';
		$countdown_style='<style>'.$this->get_css('wpdevart_countdown_'.self::$id_counter).'</style>';
		return $countdown_html.$countdown_script.$countdown_style;
	}	
	
	private function get_css($main_id){
		$main_id="#".$main_id;
		$css="";
		$css.=$main_id."{width:".$this->theme["countdown_global_width"].$this->theme["countdown_global_width_metrick"].";text-align:".$this->theme["countdown_horizontal_position"]."}";
		$css.=$main_id." .wpdevart_countdown_extend_element{min-width:".$this->theme["countdown_standart_elements_width"]."px;text-align:center;}";
		$css.=$main_id." .wpdevart_countdown_extend_element{margin-right:".$this->theme["countdown_standart_elements_distance"]."px;}";
		//$css.=$main_id." .wpdevart_countdown_extend_element:last-child{margin-right:0px;}";
		$css.=$main_id." .time_left_extended{";
		$css.="background-color:#ffffff;";
		$css.="font-size:".$this->theme["countdown_standart_time_font_size"]."px;";
		$css.="color:#1e73be;";
		$css.="padding:".$this->theme["countdown_standart_time_padding"]["top"]."px ".$this->theme["countdown_standart_time_padding"]["right"]."px ".$this->theme["countdown_standart_time_padding"]["bottom"]."px ".$this->theme["countdown_standart_time_padding"]["left"]."px;";
		$css.="margin:".$this->theme["countdown_standart_time_margin"]["top"]."px ".$this->theme["countdown_standart_time_margin"]["right"]."px ".$this->theme["countdown_standart_time_margin"]["bottom"]."px ".$this->theme["countdown_standart_time_margin"]["left"]."px;";
		$css.="border-width:".$this->theme["countdown_standart_time_border_width"]."px;";
		$css.="border-radius:".$this->theme["countdown_standart_time_border_radius"]."px;";
		$css.="border-color:#000000;";
		$css.="}";
		$css.=$main_id." .time_text{";
		$css.="background-color:#ffffff;";
		$css.="font-size:".$this->theme["countdown_standart_time_text_font_size"]."px;";
		$css.="color:000000;";	
		$css.="padding:".$this->theme["countdown_standart_time_text_padding"]["top"]."px ".$this->theme["countdown_standart_time_text_padding"]["right"]."px ".$this->theme["countdown_standart_time_text_padding"]["bottom"]."px ".$this->theme["countdown_standart_time_text_padding"]["left"]."px;";
		$css.="margin:".$this->theme["countdown_standart_time_text_margin"]["top"]."px ".$this->theme["countdown_standart_time_text_margin"]["right"]."px ".$this->theme["countdown_standart_time_text_margin"]["bottom"]."px ".$this->theme["countdown_standart_time_text_margin"]["left"]."px;";
		$css.="border-width:".$this->theme["countdown_standart_time_text_border_width"]."px;";
		$css.="border-radius:".$this->theme["countdown_standart_time_text_border_radius"]."px;";
		$css.="border-color:000000;";
		$css.="}";
		$css.=$main_id." .wpdevart_countdown_extend_element{";
		$css.="visibility: visible;";
		$css.="}";		
		return $css;
	}
}
?>