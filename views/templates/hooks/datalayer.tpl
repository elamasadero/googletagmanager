<!-- Google Tag Manager Data Layer -->
<script data-keepinline="true">
window.dataLayer = window.dataLayer || [];
{if $transactionId}
dataLayer.push({
    'ecommerce' : {
        'purchase' : {
            'actionField' : {
                'id': '{$transactionId}',
                'revenue' : '{$transactionTotal}',
                'shipping': '{$transactionShipping}',
                },
                'products' : [
                             {foreach from=$transactionProducts item=product}
                                {literal}
                                    {
                                {/literal}
                                'sku' : '{$product['id_product']}',
                                'name' : '{$product['name']}',
                                'category' : '{$product['category']}',
                                'price' : '{$product['price_wt']}',
                                'quantity' : '{$product['quantity']}'
                                {literal}
                                    }
                                {/literal}{if not $product@last}, {/if}
                                {/foreach}
                            ]
                }
        }
    }
);
{*Criteo One Tag Transaction ID data Layer var*}
dataLayer.push({
    'TransactionID' : '{$transactionId}'
});
{/if}
{if $PageType}
dataLayer.push({
    'PageType': '{$PageType}'
});
{/if}
{if $hashedEmail}
dataLayer.push({
    'hashedEmail': '{$hashedEmail}'
});
{/if}
{if $ProductID}
dataLayer.push({
    'ProductID': '{$ProductID}'
});
{/if}
{if $three_products}
dataLayer.push({
   'ProductIDList': [{foreach from=$three_products item=product}'{$product['id_product']}'{if not $product@last}, {/if}{/foreach}]
});
{/if}
{if $transactionProducts}
dataLayer.push({
    'ProductIDList': [{foreach from=$transactionProducts item=product}{literal}{{/literal}'id' : '{$product['id_product']}', 'price' : '{$product['price_wt']}', 'quantity':'{$product['quantity']}'{literal}}{/literal} {if not $product@last}, {/if}{/foreach}]
});
{/if}
{if $type_of_customer}
dataLayer.push({
    'typeof_c' : '{$type_of_customer}'
});
{/if}
</script>
<!-- End Google Tag Manager Data Layer -->