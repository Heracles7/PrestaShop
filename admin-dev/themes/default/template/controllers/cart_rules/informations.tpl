<div class="control-group">	
	<label class="control-label required">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='This will be displayed in the cart summary, as well as on the invoice.'}">
			{l s='Name'}
		</span>
	</label>
	<div class="controls">
		{foreach from=$languages item=language}
		<div class="input-append">
			<input id="name_{$language.id_lang|intval}" class="input-xlarge" type="text"  name="name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang|intval)|escape:html:'UTF-8'}">
			<div class="btn-group">
				<button class="btn dropdown-toggle" data-toggle="dropdown">
					<img src="http://checkout.kg/presta/img/l/1.jpg" alt=""> English
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
						<li class="active"><a href="#"><img src="http://checkout.kg/presta/img/l/1.jpg" alt=""> English</a></li>
						<li><a href="#"><img src="http://checkout.kg/presta/img/l/2.jpg" alt=""> Français</a></li>
				</ul>
			</div>
		</div>
		{/foreach}

		<div class="translatable hide">			
		{foreach from=$languages item=language}
			<div class="lang_{$language.id_lang|intval}">
				<input type="text" id="name_{$language.id_lang|intval}" name="name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang|intval)|escape:html:'UTF-8'}"/>
			</div>
		{/foreach}
		</div>
	</div>
</div>

<div class="control-group">
	<label class="control-label">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='For your eyes only. This will never be displayed to the customer.'}">
			{l s='Description'}
		</span>
	</label>
	<div class="controls">
		<textarea class="input-xxlarge" name="description" rows="2" >{$currentTab->getFieldValue($currentObject, 'description')|escape}</textarea>
	</div>
</div>

<div class="control-group">
	<label class="control-label">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='Caution! The rule will automatically be applied if you leave this field blank.'}">
			{l s='Code'}
		</span>
	</label>
	<div class="controls">
		<div class="input-append">
			<input class="input-medium" type="text" id="code" name="code" value="{$currentTab->getFieldValue($currentObject, 'code')|escape}" />
			<a href="javascript:gencode(8);" class="btn"><i class="icon-random"></i>  {l s='Generate'}</a>
		</div>
	</div>
</div>

<div class="control-group">
	<label class="control-label">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='If the voucher is not yet in the cart, it will be displayed in the cart summary.'}">
			{l s='Highlight'}
		</span>
	</label>
	<div class="controls switch">
		<input type="radio" name="highlight" id="highlight_on" value="1" {if $currentTab->getFieldValue($currentObject, 'highlight')|intval}checked="checked"{/if}/>
		<label class="t radio" for="highlight_on"><i class="icon-check-sign"></i> {l s='Yes'}</label>
		<input type="radio" name="highlight" id="highlight_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'highlight')|intval}checked="checked"{/if} />
		<label class="t radio" for="highlight_off"><i class="icon-ban-circle"></i> {l s='No'}</label>
		<span class="slide-button btn"></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='Only applicable if the voucher value is greater than the cart total.'}
		{l s='If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.'}">
			{l s='Partial use'}
		</span>
	</label>
	<div class="controls switch">
		<input type="radio" name="partial_use" id="partial_use_on" value="1" {if $currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
		<label class="t radio" for="partial_use_on"><i class="icon-check-sign"></i> {l s='Yes'}</label>
		<input type="radio" name="partial_use" id="partial_use_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
		<label class="t radio" for="partial_use_off"><i class="icon-ban-circle"></i> {l s='No'}</label>
		<span class="slide-button btn"></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".'}">
			{l s='Priority'}
		</span>
	</label>
	<div class="controls">
		<input type="text" class="input-mini" name="priority" value="{$currentTab->getFieldValue($currentObject, 'priority')|intval}" />
	</div>
</div>

<div class="control-group">
	<label class="control-label">{l s='Status'}</label>
	<div class="controls switch">
		<input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
		<label class="t radio" for="active_on"><i class="icon-check-sign"></i> {l s='Yes'}</label>
		<input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
		<label class="t radio" for="active_off"><i class="icon-ban-circle"></i> {l s='No'}</label>
		<span class="slide-button btn"></span>
	</div>
</div>
<hr/>