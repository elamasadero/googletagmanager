# Instalación

Añade los hooks personalizados GoogleTagManagerOnTop y GoogleTagManagerAfterBody del header.tpl de tu theme

    <head>
    {hook h="googletagmanagerontop" module="googletagmanager"}
    <title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
    ...    
    <body id="{$page_name|escape:'htmlall':'UTF-8'}">
    {hook h="googletagmanagerafterbody" module="googletagmanager"}
    
Coloca la carpeta googletagmanager/ en tu directorio modules/ de Prestashop y activa desde el back-office.

Define tu ID de Google Tag Manager en la configuración del módulo.

Configura Google Tag Manager para leer correctamente las variables del dataLayer

# Variables del dataLayer
Esta versión del módulo envía al dataLayer los siguientes valores
        
        transactionId
        transactionTotal
        transactionShipping
        products: array con { id_product, name, category, price, quantity }
        PageType: [Homepage|ProductPage|ListingPage|BasketPage|TransactionPage]
        hashedEmail
        ProductID: id del producto
        ProductIDList: array de id de productos 
        'typeof_c': [new_customer|returning_customer]

# Importante
Este módulo solo se ha probado en Prestashop 1.6.0.5.

# Donaciones
Si este módulo te ha ahorrado algunas horas de trabajo y quieres agradecérmelo, puedes enviarme un regalo de [mi lista de deseos de Amazon](http://amzn.eu/7x6Fz4G).