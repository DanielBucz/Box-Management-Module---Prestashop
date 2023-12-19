<div class="panel">
    <h3><i class="icon icon-info"></i> {l s='Dodawanie boksów' mod='boksy'}</h3>
    <form action="{$admin_module_link}&configure=boksy" method="post" enctype="multipart/form-data">
        <label for="background">{l s='Background Image' mod='boksy'}:</label>
        <input type="file" name="background" id="background" accept="image/*" />
        <br />
        {if $box[0].background !=''}
        <img src="{$box[0].background}" alt="">
        <input type="hidden" name="background_previous" id="background_previous" value="{$box[0].background}" />
        {/if}
        </br>
        <input type="hidden" name="id" id="id" value="{$box[0].id_boksy}" />
        <label for="title">{l s='Box Title' mod='boksy'}:</label>
        <input type="text" name="title" id="title" value="{$box[0].title}" />
        <br />
        <label for="link-home">{l s='Link to Home Page' mod='boksy'}:</label>
        <select name="link-home" id="link-home">
            <option value="1" {if $box[0].link_home=='1' }selected="selected" } {/if}>{l s='Yes' mod='boksy'}</option>
            <option value="0" {if $box[0].link_home=='0' }selected="selected" } {/if}>{l s='No' mod='boksy'}</option>
        </select>
        <br />
        <label for="link-product">{l s='Link to Product' mod='boksy'}:</label>
        <select name="link-product" id="link-product">
            <option value="">{l s='Choose a product' mod='boksy'}</option>
            {foreach $products_array as $product}
            <option value="{$product.id}" {if $box[0].link_product==$product.id}selected="selected" {/if}>
                {$product.name} ({$product.type})</option>
            {/foreach}
        </select>
        <br />

        <label for="link-cms">{l s='Link to Static Page' mod='boksy'}:</label>
        <select name="link-cms" id="link-cms">
            <option value="">{l s='Choose a static page' mod='boksy'}</option>
            {foreach $cmsList as $cms}
            <option value="{$cms.id}" {if $box[0].link_cms==$cms.id}selected="selected" {/if}>{$cms.name}</option>
            {/foreach}
        </select>
        <br />
        <label for="category-tree">{l s='Link to Category' mod='boksy'}:</label>
        {$tree_html}
        <br />
        <label for="url-box">{l s='URL (Strona do której będzie przekierowywał box)' mod='boksy'}:</label>
        <input type="text" name="url" id="url">
        <br/>
        <input type="submit" name="submitEditBoks" value="{l s='Save' mod='boksy'}" class="button" />

    </form>
</div>