{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2023 PrestaShop SA
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
    <div class="panel">     
        <h3><i class="icon icon-info"></i> {l s='Dodawanie boksów' mod='boksy'}</h3>
        <p>{l s='Moduł do zarządzania boksami' mod='boksy'}</p>

    </div>
    <div class="panel"><h3><i class="icon-list-ul"></i> {l s='Lista boksów' mod='boksy'}
        <span class="panel-heading-action">
            <a id="desc-product-new" class="list-toolbar-btn addBox" href="{$admin_module_link}&configure=boksy&addBox=1">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new' d='Admin.Actions'}" data-html="true">
                    <i class="process-icon-new "></i>
                </span>
            </a>
        </span>
        </h3>
        <div id="slides">
            <div " class="panel">
            <div class="row">
                <div class="col-lg-1">
                  
                </div>
                <div class="col-md-3">
                    Tło
                </div>
                <div class="col-md-8">
                    Tytuł
                </div>
            </div>
        </div>
			{foreach from=$boxes item=box}
				<div id="boxes_{$box.id_boksy}" class="panel">
                    
					<div class="row">
						<div class="col-lg-1">
							<span>#{$box.id_boksy}</span>
						</div>
						<div class="col-md-3">
							<img src="{$box.background}" alt="" class="img-thumbnail" />
						</div>
						<div class="col-md-8">
							{$box.title}
							<div class="btn-group-action pull-right">
								<a class="btn btn-default"
									href="{$admin_module_link}&configure=boksy&edit_id_box={$box.id_boksy}">
									<i class="icon-edit"></i>
									{l s='Edit' d='Admin.Actions'}
								</a>
								<a class="btn btn-default"
									href="{$admin_module_link}&configure=boksy&delete_id_box={$box.id_boksy}">
									<i class="icon-trash"></i>
									{l s='Delete' d='Admin.Actions'}
								</a>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
    </div>
    