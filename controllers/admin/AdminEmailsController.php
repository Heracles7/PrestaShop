<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminEmailsControllerCore extends AdminController
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		foreach (Contact::getContacts($this->context->language->id) as $contact)
			$arr[] = array('email_message' => $contact['id_contact'], 'name' => $contact['name']);

 		$this->options = array(
			'email' => array(
				'title' => $this->l('E-mail'),
				'icon' => 'email',
				'fields' =>	array(
					'PS_MAIL_EMAIL_MESSAGE' => array('title' => $this->l('Send e-mail to:'), 'desc' => $this->l('When customers send message from order page'), 'validation' => 'isUnsignedId', 'type' => 'select', 'cast' => 'intval', 'identifier' => 'email_message', 'list' => $arr),
					'PS_MAIL_METHOD' => array('title' => '', 'validation' => 'isGenericName', 'type' => 'radio', 'required' => true, 'choices' => array(1 => $this->l('Use PHP mail() function.  Recommended; works in most cases'), 2 => $this->l('Set my own SMTP parameters. For advanced users ONLY')), 'js' => array(1 => 'onclick="$(\'#smtp\').slideUp();"', 2 => 'onclick="$(\'#smtp\').slideDown();"'), 'visibility' => Shop::CONTEXT_ALL),
					'PS_MAIL_TYPE' => array('title' => '', 'validation' => 'isGenericName', 'type' => 'radio', 'required' => true, 'choices' => array(1 => $this->l('Send e-mail as HTML'), 2 => $this->l('Send e-mail as Text'), 3 => $this->l('Both'))),
				),
				'submit' => array()
 			),
 			'smtp' => array(
				'title' => $this->l('E-mail'),
				'icon' => 'email',
  				'top' => '<div id="smtp" style="display: '.((Configuration::get('PS_MAIL_METHOD') == 2) ? 'block' : 'none').';">',
				'bottom' => '</div>',
  				'fields' =>	array(
					'PS_MAIL_DOMAIN' => array('title' => $this->l('Mail domain:'), 'desc' => $this->l('Fully qualified domain name (keep it empty if you do not know)'), 'empty' => true, 'validation' => 'isUrl', 'size' => 30, 'type' => 'text', 'visibility' => Shop::CONTEXT_ALL),
					'PS_MAIL_SERVER' => array('title' => $this->l('SMTP server:'), 'desc' => $this->l('IP or server name (e.g., smtp.mydomain.com)'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text', 'visibility' => Shop::CONTEXT_ALL),
					'PS_MAIL_USER' => array('title' => $this->l('SMTP user:'), 'desc' => $this->l('Leave blank if not applicable'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text', 'visibility' => Shop::CONTEXT_ALL),
					'PS_MAIL_PASSWD' => array('title' => $this->l('SMTP password:'), 'desc' => $this->l('Leave blank if not applicable'), 'validation' => 'isAnything', 'size' => 30, 'type' => 'password', 'visibility' => Shop::CONTEXT_ALL, 'autocomplete' => false),
					'PS_MAIL_SMTP_ENCRYPTION' => array('title' => $this->l('Encryption:'), 'desc' => $this->l('Use an encrypt protocol'), 'type' => 'select', 'cast' => 'strval', 'identifier' => 'mode', 'list' => array(array('mode' => 'off', 'name' => $this->l('None')), array('mode' => 'tls', 'name' => $this->l('TLS')), array('mode' => 'ssl', 'name' => $this->l('SSL'))), 'visibility' => Shop::CONTEXT_ALL),
					'PS_MAIL_SMTP_PORT' => array('title' => $this->l('Port:'), 'desc' => $this->l('Number of port to use'), 'validation' => 'isInt', 'size' => 5, 'type' => 'text', 'cast' => 'intval', 'visibility' => Shop::CONTEXT_ALL),
 				),
 				'submit' => array()
 			),
 			'test' => array(
				'title' =>	$this->l('Test your e-mail configuration'),
				'icon' =>	'email',
				'fields' =>	array(
					'PS_SHOP_EMAIL' => array('title' => $this->l('Send a test e-mail to'), 'type' => 'text', 'size' => 40, 'id' => 'testEmail'),
				),
				'bottom' => '<div class="margin-form"><input type="button" class="button" name="btEmailTest" id="btEmailTest" value="'.$this->l('Send an e-mail test').'" onclick="verifyMail();" /><br />
					<p id="mailResultCheck" style="display:none;"></p></div>',
			)
 		);
	}

	public function beforeUpdateOptions()
	{
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionnality has been disabled.');
			return;
		}
		/* PrestaShop demo mode*/

		// We don't want to update the shop e-mail when sending test e-mails
		if (isset($_POST['PS_SHOP_EMAIL']))
			$_POST['PS_SHOP_EMAIL'] = Configuration::get('PS_SHOP_EMAIL');

		if ($_POST['PS_MAIL_METHOD'] == 2 && (empty($_POST['PS_MAIL_SERVER']) || empty($_POST['PS_MAIL_SMTP_PORT'])))
			$this->errors[] = Tools::displayError('You must define a SMTP server and a SMTP port. If you do not know, use the PHP mail() function instead.');
	}
}
