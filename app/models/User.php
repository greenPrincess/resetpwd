<?php 
class User extends \Phalcon\Mvc\Model{
	
	/*可以理解为设置表前缀
	 *按phalcon的约定，表名和模型的名字是一致的
     *但很多时候，表都会有一个表前缀，当然，我们也可以把类名命名为:Tbl_user extends Model
     *但这样很不美观，所以，getSource方法用来设置真正的表名	 
	 */
	public function getSource(){
		return "tbl_user";
	}
	
	/**
	 *通过email来获取用户信息
	 *如果查到记录，则返回true,否则返回false
	 *
	 */
	public function getUserByEmail($email){
		$user = User::findfirst("email ='" . $email . "'");		
		return $user ;
	}

	/**
	 *通过email更新is_reset_pwd字段，用来标识邮件已发送，暂未修改密码
	 *
	 */
	public function setResetPwdStatus($email){
		$user = User::findfirst("email ='" . $email . "'");//找到符合条件的email
		$user->is_reset_pwd = 0;//设置它的is_reset_pwd为0
		$user->save();//保存
	}

	/**
	 *修改密码
	 *
	 */
	public function modifyPwd($email,$pwd){
		$user = User::findfirst("email ='" . $email . "'");//找到符合条件的email
		$user->passwd = $pwd;//设置新密码
		$user->is_reset_pwd = 1;
		$user->save();//保存
	}

}