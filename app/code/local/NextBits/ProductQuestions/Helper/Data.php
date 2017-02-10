<?php
class NextBits_ProductQuestions_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_EMAIL_CONTRACT_NOTIFICATION = 'productquestions/general/notification_question';
	const XML_PATH_EMAIL_CONTRACTS_NOTIFICATION = 'productquestions/general/notification_answer';
	public function getConfiguration() {
        $pathGeneral  = 'productquestions/general/';
        $config = array(
			'ask_questions'                     => Mage::getStoreConfig($pathGeneral . 'ask_questions'),
			'answers'               			=> Mage::getStoreConfig($pathGeneral . 'answers'),
			'approve_ans'            			=> Mage::getStoreConfig($pathGeneral . 'approve_ans'),
			'approve_que'            			=> Mage::getStoreConfig($pathGeneral . 'approve_que'),
			'admin_email'          				=> Mage::getStoreConfig($pathGeneral . 'admin_email'),
            'admin_name'   						=> Mage::getStoreConfig($pathGeneral . 'admin_name'),
            'send_notification_question'     	=> Mage::getStoreConfig($pathGeneral . 'send_notification_question'),
            'send_notification_answer'  		=> Mage::getStoreConfig($pathGeneral . 'send_notification_answer'),
        );
        return $config;
    }
	
	const XML_CONFIG_PATH = 'productquestions/general/';

    /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'nbproductquestions/';

    /**
     * List files for include.
     *
     * @var array
     */
    protected $_files = array(
        'jquery.js',
        'jquery.noconflict.js',
    );

    /**
     * Check enabled.
     *
     * @return bool
     */
    public function isJqueryEnabled()
    {
        return (bool) $this->_getConfigValue('jquery', $store = '');
    }

    /**
     * Return path file.
     *
     * @param $file
     *
     * @return string
     */
    public function getJQueryPath($file)
    {
        return self::NAME_DIR_JS . $file;
    }

    /**
     * Return list files.
     *
     * @return array
     */
	public function getFiles()
    {
        return $this->_files;
    }
	
    protected function _getConfigValue($key, $store)
    {
        return Mage::getStoreConfig(self::XML_CONFIG_PATH . $key, $store = '');
    }
	
	public function whoAskQuestion()
	{
		$config = $this->getConfiguration();
		return $config['ask_questions'];
	}
	
	public function whoCanAnswer()
	{
		$config = $this->getConfiguration();
		return $config['answers'];
	}
	
	public function approveAnsAutomatically()
	{
		$config = $this->getConfiguration();
		return $config['approve_ans'];
	}
	
	public function approveQueAutomatically()
	{
		$config = $this->getConfiguration();
		return $config['approve_que'];
	}
	
	public function getEmailAddress() 
	{
		$config = $this->getConfiguration();
		return $config['admin_email'];
	}
	
	public function getName() 
	{
		$config = $this->getConfiguration();
		return $config['admin_name'];
	}
	
	public function getQuestionNotification() 
	{
		$config = $this->getConfiguration();
		return $config['send_notification_question'];
	}
	
	public function getAnswerNotification() 
	{
		$config = $this->getConfiguration();
		return $config['send_notification_answer'];
	}
	
	static public function sendMail($question) 
	{ 
	
		if(empty($question)) return;
		
		foreach ($question as $val) {
			$question = $val->getQuestions();
			$proId = $val->getProductId();
			$authorMail = $val->getAuthorEmail();
			$authorName  = $val->getAuthorName();
			$status = $val->getStatus();
		}
		$model = Mage::getModel('catalog/product')->load($proId);
		$productName = $model->getName();
		$post['status'] = $status;
		$post['product'] = $productName;
		$post['author_name'] = $authorName;
		$post['author_mail'] = $authorMail;	
		$post['question'] = $question;
		
		
		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		$mailTemplate = Mage::getModel('core/email_template');
		$templateConfigPath = self::XML_PATH_EMAIL_CONTRACT_NOTIFICATION;

		$senderMail = Mage::helper('productquestions')->getEmailAddress();
		$senderName  = Mage::helper('productquestions')->getName();
		$sentFrom = array('email'=>$senderMail,'name'=>$senderName);
		$receiver = array(
			$senderMail => $senderName,
			$authorMail => $authorName
		);
		$template = Mage::getStoreConfig($templateConfigPath, Mage::app()->getStore()->getId());
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
			->sendTransactional($template,$sentFrom,array_keys($receiver),array_values($receiver),$post);
	}
	
	static public function sendEmail($answer) 
	{ 
	
		if(empty($answer)) return;	
		
		foreach ($answer as $val) {
			$answer = $val->getAnswers();
			$queId = $val->getProductQuestionsId();
			$authorMail = $val->getAuthorEmail();
			$authorName  = $val->getAuthorName();
			$status = $val->getStatus();
		}
		
		$que = Mage::getModel('productquestions/questions')->load($queId);
		$question = $que->getQuestions();
		$proId = $que->getProductId();
		$queauthorMail = $que->getAuthorEmail();
		$queauthorName  = $que->getAuthorName();
		$model = Mage::getModel('catalog/product')->load($proId);
		$productName = $model->getName();
	
		$post['question'] = $question;
		$post['status'] = $status;
		$post['product'] = $productName;
		$post['author_name'] = $authorName;
		$post['author_mail'] = $authorMail;	
		$post['answer'] = $answer;
		$storeId = Mage::app()->getStore()->getId();
		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		$mailTemplate = Mage::getModel('core/email_template');
		$templateConfigPath = self::XML_PATH_EMAIL_CONTRACTS_NOTIFICATION;

		$senderMail = Mage::helper('productquestions')->getEmailAddress();
		$senderName  = Mage::helper('productquestions')->getName();
		$sentFrom = array('email'=>$senderMail,'name'=>$senderName);
		$receiver = array(
			$senderMail => $senderName,
			$queauthorMail => $queauthorName
		);
		$template = Mage::getStoreConfig($templateConfigPath, Mage::app()->getStore()->getId());
		$mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
			->sendTransactional($template,$sentFrom,array_keys($receiver),array_values($receiver),$post);
	}
}