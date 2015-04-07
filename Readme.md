# Canada Post

Delivery module for [Canada Post](http://www.canadapost.ca)

The module is compatible with Thelia 2.1 and greater. You can use this module if you shipped your parcel from Canada.  
You should have an account on Canada Post and register to the Developper Program to get your API keys.

For now, the module is not fully complete and only the Rating web service is integrated.
It allows you to display a list of possible Canada Post service that a customer can select if the destination is
available. The shipping costs is automatically retrieve from the Canada Post API.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is CanadaPost.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/canada-post-module:~1.0
```

## Configuration

You need to configure the module. There is a dedicated page for the configuration that you can display from the main
modules page in the back office.

You should register on the Canada Post website to get your credentials to access
the Canada Post API : <https://sso.epost.ca/sso/pfe/ui/registration>

Once you have received yous parameters, you can fill up the configuration form.
A form is available to test your configuration.  

You should also configure the shipping zones that the module could handled. The link to this page is also on the modules
page.

Finally, you have to get the list of the services available from Canada Post and select which one is available on
your website. The page is accessible from the configuration page of the module. On the configuration page of the services,
you have to first get the list of services from Canada Post. A button at the top right of the page will import this list.
Once the list is imported, you can customize the title of the service and select if it will be available for the customer with
the visible attribute.

## Hook

If your module use one or more hook, fill this part. Explain which hooks are used.

## Loop

### canada-post-rate

This loop is used to get a list of all available services and rates for a specific
destination.

#### Input arguments

|Argument |Description |
|---      |--- |
|**country** | The country code. eg: fr, ca *required* |
|**postal_code** | the postal code of the destination. It's required for Canada and USA |
|**weight** | The weight in kg of the order. *required* |

#### Output arguments

|Variable   |Description |
|---        |--- |
|$NAME | The name of the service |
|$CODE | The code of the service |
|$PRICE | Total cost of the shipment |
|$TAXES | Taxes charged for this shipment |
|$TRANSIT_TIME | The number of days from drop-off or pickup to 1st delivery attempt. |
|$DELIVERY_DATE | The estimated date of delivery, starting from the expected mailing-date. |

### canada-post-service

This loop is used to get a list of all available services. It doesn't directly talk
to the Canada Post web service but use the list imported in the back office.

#### Input arguments

|Argument |Description |
|---      |--- |
|id | The id of the service |
|visible | Visible service : **on**, **off** or **\***. default: **on** |
|code | The code of the service |
|title | The title of the service  |
|order | The order : id, id-reverse, visible, visible-reverse, code, code-reverse, title, title-reverse, chapo, chapo-reverse |

#### Output arguments

|Variable   |Description |
|---        |--- |
|$ID | The service id |
|$VISIBLE | The service visibility |
|$CODE | The service code |
|$TITLE | The service name |
|$CHAPO | The service summary |

### canada-post-order

This loop is used to get the detail of the service used for an order.

#### Input arguments

|Argument |Description |
|---      |--- |
|id | The id |
|address_id | The id of the address. This can be used when the order is not placed (cart) |
|order_address_id | The id of the order address. When the order is placed |
|service_id | The id of the Canada Post service |

#### Output arguments

|Variable   |Description |
|---        |--- |
|$ID | The id |
|$ADDRESS_ID | The address id of the customer (cart) |
|$ORDER_ADDRESS_ID | The order address id of the customer (order) |
|$SERVICE_ID | The Canada Post service id |


## Other ?

If you have other think to put, feel free to complete your readme as you want.
