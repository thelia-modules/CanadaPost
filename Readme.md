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

If your module declare one or more loop, describe them here like this :

### loop name

#### Input arguments

|Argument |Description |
|---      |--- |
|**arg1** | describe arg1 with an exemple. |
|**arg2** | describe arg2 with an exemple. |

#### Output arguments

|Variable   |Description |
|---        |--- |
|$VAR1    | describe $VAR1 variable |
|$VAR2    | describe $VAR2 variable |

#### Example

Add a complete exemple of your loop


## Other ?

If you have other think to put, feel free to complete your readme as you want.
