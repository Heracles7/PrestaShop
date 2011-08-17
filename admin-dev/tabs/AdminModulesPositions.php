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
*  @version  Release: $Revision: 7466 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminModulesPositions extends AdminTab
{
	private $displayKey = 0;

	public function postProcess()
	{
		// Getting key value for display
		if (Tools::getValue('show_modules') AND strval(Tools::getValue('show_modules')) != 'all')
			$this->displayKey = (int)(Tools::getValue('show_modules'));

		// Change position in hook
		if (array_key_exists('changePosition', $_GET))
		{
			if ($this->tabAccess['edit'] === '1')
		 	{
				$id_module = (int)(Tools::getValue('id_module'));
				$id_hook = (int)(Tools::getValue('id_hook'));
				$module = Module::getInstanceById($id_module);
				if (Validate::isLoadedObject($module))
				{
					$module->updatePosition($id_hook, (int)(Tools::getValue('direction')));
					Tools::redirectAdmin(self::$currentIndex.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
				}
				else
					$this->_errors[] = Tools::displayError('module cannot be loaded');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		// Add new module in hook
		elseif (Tools::isSubmit('submitAddToHook'))
		{
		 	if ($this->tabAccess['add'] === '1')
			{
				// Getting vars...
				$id_module = (int)(Tools::getValue('id_module'));
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)(Tools::getValue('id_hook'));
				$hook = new Hook($id_hook);

				if (!$id_module OR !Validate::isLoadedObject($module))
					$this->_errors[] = Tools::displayError('module cannot be loaded');
				elseif (!$id_hook OR !Validate::isLoadedObject($hook))
					$this->_errors[] = Tools::displayError('Hook cannot be loaded.');
				elseif (Hook::getModulesFromHook($id_hook, $id_module))
					$this->_errors[] = Tools::displayError('This module is already transplanted to this hook.');
				elseif (!$module->isHookableOn($hook->name))
					$this->_errors[] = Tools::displayError('This module can\'t be transplanted to this hook.');
				// Adding vars...
				else
				{
					if (!$module->registerHook($hook->name, Context::getContext()->shop->getListOfID()))
						$this->_errors[] = Tools::displayError('An error occurred while transplanting module to hook.');
					else
					{
						$exceptions = Tools::getValue('exceptions');
						$exceptions = (isset($exceptions[0])) ? $exceptions[0] : array();
						$exceptions = explode(',', str_replace(' ', '', $exceptions));

						foreach ($exceptions AS $except)
							if (!Validate::isFileName($except))
								$this->_errors[] = Tools::displayError('No valid value for field exceptions');

						if (!$this->_errors && !$module->registerExceptions($id_hook, $exceptions, Context::getContext()->shop->getListOfID()))
							$this->_errors[] = Tools::displayError('An error occurred while transplanting module to hook.');
					}
					
					if (!$this->_errors)
						Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		
		// Edit module from hook
		elseif (Tools::isSubmit('submitEditGraft'))
		{
		 	if ($this->tabAccess['add'] === '1')
			{
				// Getting vars...
				$id_module = (int)(Tools::getValue('id_module'));
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)(Tools::getValue('id_hook'));
				$hook = new Hook($id_hook);
				
				if (!$id_module OR !Validate::isLoadedObject($module))
					$this->_errors[] = Tools::displayError('module cannot be loaded');
				elseif (!$id_hook OR !Validate::isLoadedObject($hook))
					$this->_errors[] = Tools::displayError('Hook cannot be loaded.');
				else
				{
					$exceptions = Tools::getValue('exceptions');
					if (is_array($exceptions))
					{
						foreach ($exceptions as $id => $exception)
						{
							$exception = explode(',', str_replace(' ', '', $exception));
	
							// Check files name
							foreach ($exception AS $except)
								if (!Validate::isFileName($except))
									$this->_errors[] = Tools::displayError('No valid value for field exceptions');
									
							$exceptions[$id] = $exception;
						}

						// Add files exceptions
						if (!$module->editExceptions($id_hook, $exceptions))
							$this->_errors[] = Tools::displayError('An error occurred while transplanting module to hook.');
						
						if (!$this->_errors)
							Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
					}
					else
					{
						$exceptions = explode(',', str_replace(' ', '', $exceptions));

						// Check files name
						foreach ($exceptions AS $except)
							if (!Validate::isFileName($except))
								$this->_errors[] = Tools::displayError('No valid value for field exceptions');

						// Add files exceptions
						if (!$module->editExceptions($id_hook, $exceptions, Context::getContext()->shop->getListOfID()))
							$this->_errors[] = Tools::displayError('An error occurred while transplanting module to hook.');
						else
							Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
					}
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}

		// Delete module from hook
		elseif (array_key_exists('deleteGraft', $_GET))
		{
		 	if ($this->tabAccess['delete'] === '1')
		 	{
				$id_module = (int)(Tools::getValue('id_module'));
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)(Tools::getValue('id_hook'));
				$hook = new Hook($id_hook);
				if (!Validate::isLoadedObject($module))
					$this->_errors[] = Tools::displayError('module cannot be loaded');
				elseif (!$id_hook OR !Validate::isLoadedObject($hook))
					$this->_errors[] = Tools::displayError('Hook cannot be loaded.');
				else
				{
					if (!$module->unregisterHook($id_hook, Context::getContext()->shop->getListOfID()) OR !$module->unregisterExceptions($id_hook, Context::getContext()->shop->getListOfID()))
						$this->_errors[] = Tools::displayError('An error occurred while deleting module from hook.');
					else
						Tools::redirectAdmin(self::$currentIndex.'&conf=17'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (Tools::isSubmit('unhookform'))
		{
			if (!($unhooks = Tools::getValue('unhooks')) OR !is_array($unhooks))
				$this->_errors[] = Tools::displayError('Select a module to unhook.');
			else
			{
				foreach ($unhooks as $unhook)
				{
					$explode = explode('_', $unhook);
					$id_hook = $explode[0];
					$id_module = $explode[1];
					$module = Module::getInstanceById((int)($id_module));
					$hook = new Hook((int)($id_hook));
					if (!Validate::isLoadedObject($module))
						$this->_errors[] = Tools::displayError('module cannot be loaded');
					elseif (!$id_hook OR !Validate::isLoadedObject($hook))
						$this->_errors[] = Tools::displayError('Hook cannot be loaded.');
					else
					{
						if (!$module->unregisterHook((int)($id_hook)) OR !$module->unregisterExceptions((int)($id_hook)))
							$this->_errors[] = Tools::displayError('An error occurred while deleting module from hook.');
					}
				}
				if (!sizeof($this->_errors))
					Tools::redirectAdmin(self::$currentIndex.'&conf=17'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
			}
		}
	}

	public function display()
	{
		if (array_key_exists('addToHook', $_GET) OR array_key_exists('editGraft', $_GET) OR (Tools::isSubmit('submitAddToHook') AND $this->_errors))
			$this->displayForm();
		else
			$this->displayList();
	}

	public function displayList()
	{
		$admin_dir = dirname($_SERVER['PHP_SELF']);
		$admin_dir = substr($admin_dir, strrpos($admin_dir,'/') + 1);
		
		echo '
		<script type="text/javascript" src="../js/jquery/jquery.tablednd_0_5.js"></script>
		<script type="text/javascript">
			var token = \''.$this->token.'\';
			var come_from = \'AdminModulesPositions\';
		</script>
		<script type="text/javascript" src="../js/admin-dnd.js"></script>
		';
		echo '<a href="'.self::$currentIndex.'&addToHook'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> <b>'.$this->l('Transplant a module').'</b></a><br /><br />';

		// Print select list
		echo '
		<form>
			'.$this->l('Show').' :
			<select id="show_modules" onChange="autoUrl(\'show_modules\', \''.self::$currentIndex.'&token='.$this->token.'&show_modules=\')">
				<option value="all">'.$this->l('All modules').'&nbsp;</option>
				<option>---------------</option>';
				$modules = Module::getModulesInstalled();

				foreach ($modules AS $module)
					if ($tmpInstance = Module::getInstanceById((int)($module['id_module'])))
						$cm[$tmpInstance->displayName] = $tmpInstance;

				ksort($cm);
				foreach ($cm AS $module)
					echo '
					<option value="'.(int)($module->id).'" '.($this->displayKey == $module->id ? 'selected="selected" ' : '').'>'.$module->displayName.'</option>';
			echo '
			</select>
			<br /><br />
			<input type="checkbox" id="hook_position" onclick="autoUrlNoList(\'hook_position\', \''.self::$currentIndex.'&token='.$this->token.'&show_modules='.(int)(Tools::getValue('show_modules')).'&hook_position=\')" '.(Tools::getValue('hook_position') ? 'checked="checked" ' : '').' />&nbsp;<label class="t" for="hook_position">'.$this->l('Display non-positionable hook').'</label>
		</form>
		
		<fieldset style="width:250px;float:right"><legend>'.$this->l('Live edit').'</legend>';
		if (Shop::isMultiShopActivated() && $this->context->shop->getContextType() != Shop::CONTEXT_SHOP)
			echo '<p>'.$this->l('You have to select a shop to use live edit').'</p>';
		else
			echo '<p>'.$this->l('By clicking here you will be redirected to the front office of your shop to move and delete modules directly.').'</p>
				<br>
				<a href="'.$this->context->link->getPageLink('index', false, null, 'live_edit&ad='.$admin_dir.'&liveToken='.sha1($admin_dir._COOKIE_KEY_).((Shop::isMultiShopActivated()) ? '&id_shop='.Context::getContext()->shop->getID() : '')).'" target="_blank" class="button">'.$this->l('Run LiveEdit').'</a>';
		echo '</fieldset>';

		// Print hook list
		echo '<form method="post" action="'.self::$currentIndex.'&token='.$this->token.'">';
		$irow = 0;
		$hooks = Hook::getHooks(!(int)(Tools::getValue('hook_position')));

		echo '<div id="unhook_button_position_top"><input class="button floatr" type="submit" name="unhookform" value="'.$this->l('Unhook the selection').'"/></div>';

		$canMove = (Shop::isMultiShopActivated() && $this->context->shop->getContextType() != Shop::CONTEXT_SHOP) ? false : true;
		if (!$canMove)
			echo '<br /><div><b>'.$this->l('If you want to order / move following data, please go in shop context (select a shop in shop list)').'</b></div>';
		foreach ($hooks AS $hook)
		{
			$modules = Hook::getModulesFromHook($hook['id_hook'], $this->displayKey);

			$nbModules = count($modules);
			echo '
			<a name="'.$hook['name'].'"/>
			<table cellpadding="0" cellspacing="0" class="table width3 space'.($nbModules >= 2? ' tableDnD' : '' ).'" id="'.$hook['id_hook'].'">
			<tr class="nodrag nodrop"><th colspan="4">'.$hook['title'].' - <span style="color: red">'.$nbModules.'</span> '.(($nbModules > 1) ? $this->l('modules') : $this->l('module'));
			if ($nbModules && $canMove)
				echo '<input type="checkbox" id="Ghook'.$hook['id_hook'].'" class="floatr" style="margin-right: 2px;" onclick="hookCheckboxes('.$hook['id_hook'].', 0, this)"/>';
			if (!empty($hook['description']))
				echo '&nbsp;<span style="font-size:0.8em; font-weight: normal">['.$hook['description'].']</span>';
			echo ' <sub style="color:grey;"><i>('.$this->l('Technical name: ').$hook['name'].')</i></sub></th></tr>';

			// Print modules list
			if ($nbModules)
			{
				$instances = array();
				foreach ($modules AS $module)
					if ($tmpInstance = Module::getInstanceById((int)($module['id_module'])))
						$instances[] = $tmpInstance;
				foreach ($instances AS $position => $instance)
				{
					$position = $position + 1;
					echo '
					<tr id="'.$hook['id_hook'].'_'.$instance->id.'"'.($irow++ % 2 ? ' class="alt_row"' : '').' style="height: 42px;">';
					if (!$this->displayKey)
					{
						echo '
						<td class="positions" width="40">'.(int)($position).'</td>
						<td'.(($canMove && $nbModules >= 2) ? ' class="dragHandle"' : '').' id="td_'.$hook['id_hook'].'_'.$instance->id.'" width="40">
							'.(($canMove) ? '<a'.($position == 1 ? ' style="display: none;"' : '' ).' href="'.self::$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&direction=0&token='.$this->token.'&changePosition='.rand().'#'.$hook['name'].'"><img src="../img/admin/up.gif" alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a><br />
							<a '.($position == count($instances) ? ' style="display: none;"' : '').'href="'.self::$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&direction=1&token='.$this->token.'&changePosition='.rand().'#'.$hook['name'].'"><img src="../img/admin/down.gif" alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>' : '').'
						</td>
						<td style="padding-left: 10px;"><label class="lab_modules_positions" for="mod'.$hook['id_hook'].'_'.$instance->id.'">
						';
					}
					else
						echo '<td style="padding-left: 10px;" colspan="3"><label class="lab_modules_positions" for="'.$hook['id_hook'].'_'.$instance->id.'">';
					echo '
					<img src="../modules/'.$instance->name.'/logo.gif" alt="'.stripslashes($instance->name).'" /> <strong>'.stripslashes($instance->displayName).'</strong>
						'.($instance->version ? ' v'.((int)($instance->version) == $instance->version? sprintf('%.1f', $instance->version) : (float)($instance->version)) : '').'<br />'.$instance->description.'
					</label></td>
						<td width="60">';
						echo '
							<a href="'.self::$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&editGraft'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token.'"><img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>
							<a href="'.self::$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&deleteGraft'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token.'"><img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
							<input type="checkbox" id="mod'.$hook['id_hook'].'_'.$instance->id.'" class="hook'.$hook['id_hook'].'" onclick="hookCheckboxes('.$hook['id_hook'].', 1, this)" name="unhooks[]" value="'.$hook['id_hook'].'_'.$instance->id.'"/>';
						echo '
						</td>
					</tr>';
				}
			} else
				echo '<tr><td colspan="4">'.$this->l('No module for this hook').'</td></tr>';
			echo '</table>';
		}
		echo '<div id="unhook_button_position_bottom"><input class="button floatr" type="submit" name="unhookform" value="'.$this->l('Unhook the selection').'"/></div></form>';
	}

	public function displayForm($isMainTab = true)
	{
		parent::displayForm();

		$id_module = (int)(Tools::getValue('id_module'));
		$id_hook = (int)(Tools::getValue('id_hook'));
		if (Tools::isSubmit('editGraft'))
		{
			// Check auth for this page
			if (!$id_module || !$id_hook)
				Tools::redirectAdmin(self::$currentIndex . '&token='.$this->token);
				
			$sql = 'SELECT id_module
					FROM '._DB_PREFIX_.'hook_module
					WHERE id_module = '.$id_module.'
						AND id_hook = '.$id_hook.'
						AND id_shop IN('.implode(', ', Context::getContext()->shop->getListOfID()).')';
			if (!Db::getInstance()->getValue($sql))
				Tools::redirectAdmin(self::$currentIndex . '&token='.$this->token);

			$slModule = Module::getInstanceById($id_module);
			$exceptsList = $slModule->getExceptions($id_hook, true);
			$exceptsDiff = false;
			$excepts = '';
			if ($exceptsList)
			{
				$first = current($exceptsList);
				foreach ($exceptsList as $k => $v)
					if (array_diff($v, $first) || array_diff($first, $v))
						$exceptsDiff = true;
				
				if (!$exceptsDiff)
					$excepts = implode(', ', $first);
			}
		}
		else
		{
			$exceptsDiff = false;
			$exceptsList = Tools::getValue('exceptions', array(array()));
		}
		$modules = Module::getModulesInstalled(0);

		$instances = array();
		foreach ($modules AS $module)
			if ($tmpInstance = Module::getInstanceById($module['id_module']))
				$instances[$tmpInstance->displayName] = $tmpInstance;
		ksort($instances);
		$modules = $instances;
		$hooks = Hook::getHooks(0);
		echo '
		<form action="'.self::$currentIndex.'&token='.$this->token.'" method="post">';
		if ($this->displayKey)
			echo '<input type="hidden" name="show_modules" value="'.$this->displayKey.'" />';
		echo '<fieldset style="width:700px"><legend><img src="../img/t/AdminModulesPositions.gif" />'.$this->l('Transplant a module').'</legend>
				<label>'.$this->l('Module').' :</label>
				<div class="margin-form">
					<select name="id_module"'.(Tools::isSubmit('editGraft') ? ' disabled="disabled"' : '').'>';
					foreach ($modules AS $module)
						echo '
						<option value="'.$module->id.'" '.(($id_module == $module->id || (!$id_module && Tools::getValue('show_modules') == $module->id)) ? 'selected="selected" ' : '').'>'.stripslashes($module->displayName).'</option>';
					echo '
					</select><sup> *</sup>
				</div>
				<label>'.$this->l('Hook into').' :</label>
				<div class="margin-form">
					<select name="id_hook"'.(Tools::isSubmit('editGraft') ? ' disabled="disabled"' : '').'>';
					foreach ($hooks AS $hook)
						echo '
						<option value="'.$hook['id_hook'].'" '.($id_hook == $hook['id_hook'] ? 'selected="selected" ' : '').'>'.$hook['title'].'</option>';
					echo '
					</select><sup> *</sup>
				</div>';
					
		echo <<<EOF
			<script type="text/javascript">
			//<![CDATA
			function position_exception_add(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (!r.test(inputValue))
					$('#em_text_'+shopID).val(inputValue + ((inputValue.trim()) ? ', ' : '') + listValue);
			}
			
			function position_exception_remove(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (r.test(inputValue))
				{
					var rep = '';
					if (new RegExp(', *'+listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ','));
					else if (new RegExp(listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
					else
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
				}
			}
			//]]>
			</script>
EOF;

		// Manage exceptions
		if (!$exceptsDiff)
		{
			echo '<label>'.$this->l('Exceptions').' :</label>
					<div class="margin-form">';
			$this->displayModuleExceptionList(array_shift($exceptsList), 0);

			echo $this->l('Please specify those files for which you do not want the module to be displayed').'.<br />
						'.$this->l('Please type each filename separated by a comma').'.
						<br /><br />
					</div>';
		}
		else
		{
			echo '<label>'.$this->l('Exceptions').' :</label>
					<div class="margin-form">';
			foreach ($exceptsList as $shopID => $fileList)
				$this->displayModuleExceptionList($fileList, $shopID);
			echo $this->l('Please specify those files for which you do not want the module to be displayed').'.<br />
						'.$this->l('Please type each filename separated by a comma').'.
						<br /><br />
					</div>';
		}
		

		echo '<div class="margin-form">
				';
				if (Tools::isSubmit('editGraft'))
				{
					echo '
					<input type="hidden" name="id_module" value="'.$id_module.'" />
					<input type="hidden" name="id_hook" value="'.$id_hook.'" />';
				}
				echo '
					<input type="submit" value="'.$this->l('Save').'" name="'.(Tools::isSubmit('editGraft') ? 'submitEditGraft' : 'submitAddToHook').'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	
	public function displayModuleExceptionList($fileList, $shopID)
	{
		if (!is_array($fileList))
			$fileList = ($fileList) ? array($fileList) : array();

		echo '<input type="text" name="exceptions['.$shopID.']" size="40" value="' . implode(', ', $fileList) . '" id="em_text_'.$shopID.'">';
		if ($shopID)
			echo ' ('.Shop::getInstance($shopID)->name.')';
		echo '<br /><select id="em_list_'.$shopID.'">';
		
		// @todo do something better with controllers
		$controllers = Dispatcher::getControllers();
		ksort($controllers);
		foreach ($controllers as $k => $v)
		{
			echo '<option value="'.$k.'">'.$k.'</option>';
		}
		echo '</select> <input type="button" class="button" value="'.$this->l('Add').'" onclick="position_exception_add('.$shopID.')" /> 
				<input type="button" class="button" value="'.$this->l('Remove').'" onclick="position_exception_remove('.$shopID.')" /><br /><br />';
	}
}
