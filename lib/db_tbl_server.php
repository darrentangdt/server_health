<?php

class db_tbl_server
{
	public static function find_objs_all($ip = '')
	{
		if ($ip == '') {
			$sql = 'select * from tbl_server';
			$binds = array();
		} else {
			$sql = 'select * from tbl_server where ip=:ip';
			$binds = array(
				':ip'=>$ip,
			);
		}
		
		$sql .= ' order by identify_updated desc';
		
		$objs = db_tbl::find($sql, $binds);
		
		return $objs;
	}
	
	public static function find_objs_all_auth_n()
	{
		$sql = 'select * from tbl_server where auth=:auth order by identify_updated desc';
		$binds = array(
			':auth'=>constant('AUTH_N'),
		);
		
		$objs = db_tbl::find($sql, $binds);
		
		return $objs;
	}
	
	public static function find_objs_all_auth_y_ip($ip = '')
	{
		if ($ip == '') {
			$sql = 'select * from tbl_server where auth=:auth';
			$binds = array(
				':auth'=>constant('AUTH_Y'),
			);
		} else {
			$sql = 'select * from tbl_server where auth=:auth and ip=:ip';
			$binds = array(
				':auth'=>constant('AUTH_Y'),
				':ip'=>$ip,
			);
		}
		
		$sql .= ' order by inet_aton(ip) asc';
		
		$objs = db_tbl::find($sql, $binds);
		
		return $objs;
	}
	
	public static function find_objs_all_auth_y_updated($ip = '')
	{
		if ($ip == '') {
			$sql = 'select * from tbl_server where auth=:auth';
			$binds = array(
				':auth'=>constant('AUTH_Y'),
			);
		} else {
			$sql = 'select * from tbl_server where auth=:auth and ip=:ip';
			$binds = array(
				':auth'=>constant('AUTH_Y'),
				':ip'=>$ip,
			);
		}
		
		$sql .= ' order by health_updated asc';
		
		$objs = db_tbl::find($sql, $binds);
		
		return $objs;
	}
	
	public static function find_obj_by_id($ip)
	{
		$sql = 'select * from tbl_server where ip=:ip';
		$binds = array(
			':ip'=>$ip,
		);
		
		$obj = db_tbl::find_one($sql, $binds);
		
		return $obj;
	}
	
	public static function insert_obj_identify($ip, $so)
	{
		$sql = 'insert into tbl_server(ip, so, auth, identify_updated, health_updated, health_out, health_ret) values(:ip, :so, :auth, now(), now(), :health_out, :health_ret)';
		$binds = array(
			':ip'=>$ip,
			':so'=>$so,
			':auth'=>constant('AUTH_N'),
			':health_out'=>'PENDING',
			':health_ret'=>constant('RET_UNKNOWN'),
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function update_obj_identify($ip, $so)
	{
		$sql = 'update tbl_server set so=:so, auth=:auth, identify_updated=now(), health_updated=now(), health_out=:health_out, health_ret=:health_ret where ip=:ip';
		$binds = array(
			':ip'=>$ip,
			':so'=>$so,
			':auth'=>constant('AUTH_N'),
			':health_out'=>'PENDING',
			':health_ret'=>constant('RET_UNKNOWN'),
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function update_obj_auth_y($ip, $username, $password)
	{
		$sql = 'update tbl_server set username=:username, password=:password, auth=:auth, identify_updated=now() where ip=:ip';
		$binds = array(
			':ip'=>$ip,
			':username'=>$username,
			':password'=>$password,
			':auth'=>constant('AUTH_Y'),
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function update_obj_auth_n($ip)
	{
		$sql = 'update tbl_server set auth=:auth, identify_updated=now() where ip=:ip';
		$binds = array(
			':ip'=>$ip,
			':auth'=>constant('AUTH_N'),
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function update_obj_health($ip, $result)
	{
		$sql = 'update tbl_server set health_ret=:health_ret, health_out=:health_out, health_updated=now() where ip=:ip';
		$binds = array(
			':health_ret'=>$result['ret'], 
			':health_out'=>$result['out'],
			':ip'=>$ip,
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function update_obj_ext2($ip, $result)
	{
		$sql = 'update tbl_server set ext2=:ext2 where ip=:ip';
		$binds = array(
			':ext2'=>$result['out'],
			':ip'=>$ip,
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function update_obj_name($ip, $name)
	{
		$sql = 'update tbl_server set name=:name where ip=:ip';
		$binds = array(
			':name'=>$name, 
			':ip'=>$ip,
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function update_obj_ext1($ip, $ext1)
	{
		$sql = 'update tbl_server set ext1=:ext1 where ip=:ip';
		$binds = array(
			':ext1'=>$ext1, 
			':ip'=>$ip,
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
	
	public static function delete_obj_by_id($ip)
	{
		$sql = 'delete from tbl_server where ip=:ip';
		$binds = array(
			':ip'=>$ip,
		);
		
		$row_count = db_tbl::execute($sql, $binds);
		
		return $row_count;
	}
}