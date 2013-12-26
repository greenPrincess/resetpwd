<?php

//include_once($_SERVER["DOCUMENT_ROOT"] . "/api/v1/users_inc.php");

class IndexController extends ControllerBase
{

	public function indexAction()
	{
		$this->loadCustomTrans('index');
		parent::initialize();
	}

	// public function sendAction(){
	// 	$this->sendMail('lx.xin@qq.com',$url);
	// }
	protected function forward($uri){
    	$uriParts = explode('/', $uri);
    	return $this->dispatcher->forward(
    		array(
    			'controller' => $uriParts[0], 
    			'action' => $uriParts[1]
    		)
    	);
    }

	public function commitAction()
	{
		$email = $this->request->getPost('email', 'email');
		
		if(empty($email))
		{
			$this->flash->error("Your email is invalid!");
			return;
		}
		
		$user = new User();//实例化模型	
		$u  = 	$user->getUserByEmail($email);
		if(!$u){//调用模型方法，用来判断用户是否存在
			$this->flash->error("not get you email info");
			return;
		}

		//加密
		//先base64加密，加密串为email+当前时间戳+uid
		//因为要在url里传输，所以要urlencode一下，防止因为一些特殊字符转义失败
		$secret = urlencode(base64_encode($email.'\t'.time().'\t'.$u->id));

		//$url = new Phalcon\Mvc\Url;		
		$link =  $this->url->get('index/resetpwd',array('secret'=>$secret));
		die($link);
		//return $this->forward('index/mailsent');
		//发送邮件
		if($this->sendMail($email,$link) !== false){
			//send email
			//$mail->send($email,$title,$content);
			//更新密码重置状态
			$user->setResetPwdStatus($email);
			$this->flash->error("email send success!");
		}
		else{
			$this->flash->error("email send failed!");
		}		

		 
		
		/*$userOp = new OperatorUsers();
		$ret = $userOp->reset_password($email); var_dump($ret);
		if($ret['error'] == 0)
		{
			echo "Reset password succeeded, please check your mailbox and login with new password!";
		}
		else
		{
			echo "Sorry, reset password Failed!";
		}*/
		
	}
	/*密码重置*/
	public function resetpwdAction(){
		$user = new User();//实例化模型		
		if($this->request->isPost()){//暂时不安全，需要一个签名验证，比如在上面的加密串里再加一个参数，用来标识，相当于token
			$email = $this->request->getPost('email');
			$pwd = $this->request->getPost('pwd');
			$pwd2 = $this->request->getPost('pwd2');
			$token = $this->request->getPost('token');
			//var_dump($_POST);die;
			if(empty($email) || empty($pwd) || empty($pwd2)){//三个参数不能为空
				$this->flash->error("Parameter is not complete!");
				exit;
			}

			if($pwd != $pwd2){//两次输入的密码是否一致
				$this->flash->error("Two input password is not consistent!");
				exit;					
			}

			$u = $user->getUserByEmail($email);
			if($token != md5($u->id)){
				$this->flash->error("Unauthorized access!");
				exit;
			}

			//修改密码
			$user->modifyPwd($email,$pwd);
			return $this->forward('index/success');
			//echo "Congratulations,Modify Success";
			exit;
		}
		else{
			$secret = $this->request->get('secret');
			$info = explode('\t',base64_decode(urldecode($secret)));
			list($email,$time,$id) = $info;
			if(empty($email) || empty($time) || empty($id)){
				$this->flash->error("Parameter is not complete!");
				exit;
			}
			$u = $user->getUserByEmail($email);
			if(!$u){//调用模型方法，用来判断用户是否存在
				$this->flash->error("not get you email info");
				exit;
			}

			if($u->is_reset_pwd){
				$this->flash->error("You already modify password!");
				exit;
			}

			$dateT = time() - (int)$time; //现在的时间 - 修改密码链接的生成时间 = 时间差
			//默认修改密码链接为2小时 2小时=7200秒

			if($dateT > 7200){
				$this->flash->error("A link failure");//链接失效
				exit;
			}

			$this->view->setVar('email',$email);
			$this->view->setVar('token',md5($id));
		}

	}

	private function sendMail($to,$url,$from='fserver@126.com'){		
		$mail = new PHPMailer();
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		//$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = "smtp.126.com";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 25;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = $from;
		//Password to use for SMTP authentication
		$mail->Password = "3344521";
		//Set who the message is to be sent from
		$mail->setFrom($from, 'fserver');
		$mail->addAddress($to);
		//Set the subject line
		$mail->Subject = 'Sina Account Password Modify Mail';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body

		$content = "<a href='". $url ."' target='_blank'>Please click here</a>,modify your password or copy this link to Your browser's address bar
					and press enter key! <br />".$url." <p style='color:red;'>Warning:After change the password, please delete this email!!!</p>
		            ";

		$mail->Body    = $content;
		//Replace the plain text body with one created manually
		$mail->AltBody = 'Sina Account Password Modify Mail';
		//Attach an image file

		return $mail->send();
		//send the message, check for errors
		// if (!$mail->send()) {
		//     echo "Mailer Error: " . $mail->ErrorInfo;
		// } else {
		//     echo "Message sent!";
		// }
	}

	public function successAction(){
		$this->loadCustomTrans('success');
		parent::initialize();
	}

	public function mailsentAction(){
		$this->view->setVar('email',$link);
	}

	public function setLanguageAction($language='')
    {
        //Change the language, reload translations if needed
        if ($language == 'en' || $language == 'ja') {
            $this->session->set('language', $language);
            $this->loadCustomTrans('index');
        }

        //Go to the last place
        $referer = $this->request->getHTTPReferer();
        if (strpos($referer, $this->request->getHttpHost()."/")!==false) {
            return $this->response->setHeader("Location", $referer);
        } else {
            return $this->dispatcher->forward(array('controller' => 'index', 'action' => 'index'));
        }
    }
}
